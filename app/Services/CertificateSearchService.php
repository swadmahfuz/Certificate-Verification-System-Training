<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class CertificateSearchService
{
    public function applySearch(Builder $query, ?string $userInput): Builder
    {
        $userInput = $userInput ?? '';

        if ($userInput === '') {
            return $query;
        }

        $likeColumns = config('cvs.certificate_search.like', []);
        $exactColumns = config('cvs.certificate_search.exact', []);
        $dateColumns = config('cvs.certificate_search.date_like', []);

        return $query->where(function (Builder $search) use ($userInput, $likeColumns, $exactColumns, $dateColumns) {
            foreach ($likeColumns as $column) {
                $search->orWhereRaw('LOWER(' . $column . ') LIKE ?', ['%' . strtolower($userInput) . '%']);
            }

            foreach ($exactColumns as $column) {
                $search->orWhere($column, $userInput);
            }

            foreach ($dateColumns as $column) {
                $search->orWhereRaw($column . ' LIKE ?', ['%' . $userInput . '%']);
            }
        });
    }

    public function paginate(Builder $query, ?string $userInput, int $perPage = 100)
    {
        $this->applySearch($query, $userInput ?? '');

        return $query
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }
}
