<?php

declare(strict_types=1);

namespace App\Livewire\Properties;

use App\Models\Property;
use Illuminate\Validation\Rule as ValRule;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Search extends Component
{
    use WithPagination;

    #[Rule('required|in:sales,lettings')]
    public string $channel = 'lettings';

    // URL-synced filters
    #[Url(as: 'q')]
    #[Rule('nullable|string|min:2|max:80')]
    public ?string $search = null;

    #[Url]
    #[Rule('nullable|integer|min:0|max:10')]
    public ?int $bedrooms = null;

    #[Url(as: 'min_price')]
    #[Rule('nullable|integer|min:0')]
    public ?int $priceMin = null;

    #[Url(as: 'max_price')]
    #[Rule('nullable|integer|min:0|gte:priceMin')]
    public ?int $priceMax = null;

    #[Url]
    #[Rule('array|max:20')]
    public array $features = []; // reserved for future UI

    #[Url]
    #[Rule('nullable|string|in:newest,price_asc,price_desc,beds_asc,beds_desc')]
    public ?string $sort = 'newest';

    // Include inactive (applied/reserved/SSTC) listings; default OFF
    #[Url(as: 'incl_inactive')]
    #[Rule('boolean')]
    public bool $includeInactive = false;

    /** Normalise incoming values each request cycle */
    public function hydrate(): void
    {
        foreach (['bedrooms', 'priceMin', 'priceMax'] as $p) {
            if ('' === $this->{$p}) {
                $this->{$p} = null;
            }
        }

        if ( ! empty($this->features)) {
            $this->features = array_values(
                array_intersect($this->features, $this->allowedFeatures()),
            );
        }

        if ( ! in_array($this->channel, ['sales', 'lettings'], true)) {
            $this->channel = 'lettings';
        }

        if ( ! in_array($this->sort, ['newest', 'price_asc', 'price_desc', 'beds_asc', 'beds_desc'], true)) {
            $this->sort = 'newest';
        }
    }

    /** Reset pagination + validate only the changed prop */
    public function updated($name, $value): void
    {
        if (in_array($name, ['bedrooms', 'priceMin', 'priceMax'], true) && '' === $value) {
            $this->{$name} = null;
        }

        $this->validateOnly($name, array_merge($this->rules(), [
            'features.*' => ['string', ValRule::in($this->allowedFeatures())],
        ]));

        $this->resetPage();
    }

    /** Computed: whether any filters are active (for disabling the Reset button) */
    public function getHasActiveFiltersProperty(): bool
    {
        return ! empty($this->search)
            || ! empty($this->bedrooms)
            || ! empty($this->priceMin)
            || ! empty($this->priceMax)
            || ! empty($this->features)
            || ($this->sort && 'newest' !== $this->sort)
            || true === $this->includeInactive;
    }

    public function render()
    {
        // Harden all URL inputs each render
        $this->validate($this->rules());

        $q = Property::query()
            ->select('properties.*')     // avoid duplicate columns from joins
            ->distinct()                 // protect against join duplicates
            ->withPrimaryMedia();

        // Constrain by channel (prevents cross-over)
        if ('sales' === $this->channel) {
            $q->whereNotNull('properties.price_sales');
        } else {
            $q->whereNotNull('properties.price_lettings');
        }

        // Active/inactive filter
        if ($this->includeInactive) {
            $q->whereIn('properties.is_active', [1, 0]);
        } else {
            $q->where('properties.is_active', 1);
        }

        // Bedrooms (min)
        if (null !== $this->bedrooms) {
            $q->where('properties.bedrooms', '>=', $this->bedrooms);
        }

        // Price range
        $priceCol = 'sales' === $this->channel ? 'properties.price_sales' : 'properties.price_lettings';
        $min = $this->priceMin ?? 0;
        $max = $this->priceMax ?? PHP_INT_MAX;
        if ($max < $min) {
            $max = $min;
        }
        $q->when($min > 0 || $max < PHP_INT_MAX, fn ($w) => $w->whereBetween($priceCol, [$min, $max]));

        // Keyword search (optional)
        if ($this->search) {
            $term = '%' . mb_trim($this->search) . '%';
            $q->where(function ($w) use ($term): void {
                $w->where('properties.address_single_line', 'like', $term)
                    ->orWhere('properties.address_postcode', 'like', $term)
                    ->orWhere('properties.address_town', 'like', $term);
            });
        }

        // Features (example JSON contains; adjust to your schema)
        if ( ! empty($this->features)) {
            foreach ($this->features as $feature) {
                $q->whereJsonContains('properties.features', $feature);
            }
        }

        // Sort
        $this->applySorting($q);

        // Faster pagination
        $properties = $q->simplePaginate();

        return view('livewire.properties.search', [
            'properties' => $properties,
            'channel' => $this->channel,
        ]);
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'bedrooms',
            'priceMin',
            'priceMax',
            'features',
            'sort',
            'includeInactive',
        ]);

        $this->sort = 'newest';
        $this->includeInactive = false;
    }

    /** Whitelist of feature slugs allowed from the UI */
    protected function allowedFeatures(): array
    {
        return [
            'garden', 'garage', 'parking', 'balcony', 'new_build',
            'furnished', 'unfurnished', 'pet_friendly', 'ensuite',
        ];
    }

    /** Full validation rules */
    protected function rules(): array
    {
        return [
            'channel' => ['required', ValRule::in(['sales', 'lettings'])],
            'search' => ['nullable', 'string', 'min:2', 'max:80'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:10'],
            'priceMin' => ['nullable', 'integer', 'min:0'],
            'priceMax' => ['nullable', 'integer', 'min:0', 'gte:priceMin'],
            'features' => ['array', 'max:20'],
            'sort' => ['nullable', ValRule::in(['newest', 'price_asc', 'price_desc', 'beds_asc', 'beds_desc'])],
            'includeInactive' => ['boolean'],
        ];
    }

    /** Whitelisted sort mapping */
    private function sortMapping(): array
    {
        $priceCol = 'sales' === $this->channel ? 'properties.price_sales' : 'properties.price_lettings';

        return [
            'newest' => ['properties.created_at', 'desc'],
            'price_asc' => [$priceCol, 'asc'],
            'price_desc' => [$priceCol, 'desc'],
            'beds_asc' => ['properties.bedrooms', 'asc'],
            'beds_desc' => ['properties.bedrooms', 'desc'],
        ];
    }

    private function applySorting($query): void
    {
        $map = $this->sortMapping();
        [$col, $dir] = $map[$this->sort ?? 'newest'] ?? $map['newest'];
        $query->orderBy($col, $dir);
    }
}
