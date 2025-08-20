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
    public array $features = [];

    #[Url]
    #[Rule('nullable|string|in:newest,price_asc,price_desc,beds_asc,beds_desc')]
    public ?string $sort = 'newest';

    #[Url(as: 'incl_inactive')]
    #[Rule('boolean')]
    public bool $includeInactive = false;

    public function mount(): void
    {
        $this->loadFiltersFromSessionIfNoQuery();
        $this->hydrate();
    }

    public function hydrate(): void
    {
        foreach (['bedrooms', 'priceMin', 'priceMax'] as $p) {
            if ('' === $this->{$p}) {
                $this->{$p} = null;
            }
        }

        if ( ! empty($this->features)) {
            $allowed = ['garden', 'garage', 'parking', 'balcony', 'new_build', 'furnished', 'unfurnished', 'pet_friendly', 'ensuite'];
            $this->features = array_values(array_intersect($this->features, $allowed));
        }

        if ( ! in_array($this->channel, ['sales', 'lettings'], true)) {
            $this->channel = 'lettings';
        }

        if ( ! in_array($this->sort, ['newest', 'price_asc', 'price_desc', 'beds_asc', 'beds_desc'], true)) {
            $this->sort = 'newest';
        }
    }

    public function updated($name, $value): void
    {
        if (in_array($name, ['bedrooms', 'priceMin', 'priceMax'], true) && '' === $value) {
            $this->{$name} = null;
        }

        $this->validateOnly($name, array_merge($this->rules(), [
            'features.*' => ['string', ValRule::in(['garden', 'garage', 'parking', 'balcony', 'new_build', 'furnished', 'unfurnished', 'pet_friendly', 'ensuite'])],
        ]));

        $this->resetPage();
        $this->storeFiltersInSession();
    }

    public function getHasActiveFiltersProperty(): bool
    {
        return ! empty($this->bedrooms)
            || ! empty($this->priceMin)
            || ! empty($this->priceMax)
            || ! empty($this->features)
            || ($this->sort && 'newest' !== $this->sort)
            || true === $this->includeInactive;
    }

    public function render()
    {
        $this->validate($this->rules());
        $this->storeFiltersInSession();

        $q = Property::query()
            ->select('properties.*')
            ->distinct()
            ->withPrimaryMedia();

        if ('sales' === $this->channel) {
            $q->whereNotNull('properties.price_sales');
        } else {
            $q->whereNotNull('properties.price_lettings');
        }

        $q->when(
            $this->includeInactive,
            fn ($w) => $w->whereIn('properties.is_active', [1, 0]),
            fn ($w) => $w->where('properties.is_active', 1)
        );

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

        // Features (example JSON contains)
        if ( ! empty($this->features)) {
            foreach ($this->features as $feature) {
                $q->whereJsonContains('properties.features', $feature);
            }
        }

        $this->applySorting($q);

        $properties = $q->simplePaginate(24);

        return view('livewire.properties.search', [
            'properties' => $properties,
            'channel' => $this->channel,
        ]);
    }

    public function resetFilters(): void
    {
        $this->reset([
            'bedrooms', 'priceMin', 'priceMax', 'features', 'sort', 'includeInactive',
        ]);

        $this->sort = 'newest';
        $this->includeInactive = false;

        session()->forget($this->sessionKey());

        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'channel' => ['required', ValRule::in(['sales', 'lettings'])],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:10'],
            'priceMin' => ['nullable', 'integer', 'min:0'],
            'priceMax' => ['nullable', 'integer', 'min:0', 'gte:priceMin'],
            'features' => ['array', 'max:20'],
            'sort' => ['nullable', ValRule::in(['newest', 'price_asc', 'price_desc', 'beds_asc', 'beds_desc'])],
            'includeInactive' => ['boolean'],
        ];
    }

    private function sessionKey(): string
    {
        return "search.filters.{$this->channel}";
    }

    private function urlAliasMap(): array
    {
        return [
            'priceMin' => 'min_price',
            'priceMax' => 'max_price',
            'includeInactive' => 'incl_inactive',
        ];
    }

    private function persistedFilterKeys(): array
    {
        return ['bedrooms', 'priceMin', 'priceMax', 'features', 'sort', 'includeInactive'];
    }

    private function loadFiltersFromSessionIfNoQuery(): void
    {
        $saved = session($this->sessionKey(), []);
        if (empty($saved) || ! is_array($saved)) {
            return;
        }

        $qs = request()->query();
        $aliases = $this->urlAliasMap();

        foreach ($this->persistedFilterKeys() as $prop) {
            $qsKey = $aliases[$prop] ?? $prop;

            $providedInUrl = array_key_exists($qsKey, $qs) && '' !== $qs[$qsKey] && null !== $qs[$qsKey];

            if ( ! $providedInUrl && array_key_exists($prop, $saved)) {
                $this->{$prop} = $saved[$prop];
            }
        }
    }

    private function storeFiltersInSession(): void
    {
        $payload = [];
        foreach ($this->persistedFilterKeys() as $prop) {
            $payload[$prop] = $this->{$prop};
        }
        session()->put($this->sessionKey(), $payload);
    }

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
