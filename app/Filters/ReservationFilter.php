<?php

namespace App\Filters;

use Ambengers\QueryFilter\AbstractQueryFilter;

class ReservationFilter extends AbstractQueryFilter
{
    /**
     * Relationship loader class.
     *
     * @var string
     */
    protected $loader = '';

    /**
     * Columns that are searchable.
     *
     * @var array
     */
    protected $searchableColumns = [
    ];

    /**
     * List of object filters.
     *
     * @var array
     */
    protected $filters = [
        //
    ];

    public function status($status)
    {
        return $this->builder->where('status', $status);
    }

    public function paymentStatus($status)
    {
       return $this->builder->whereHas('orders.payments', function ($query) use ($status) {
             $query->where('status', 'DUE');
        });
    }
}
