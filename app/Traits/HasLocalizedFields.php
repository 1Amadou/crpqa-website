<?php
namespace App\Traits;

trait HasLocalizedFields
{
    public function getLocalizedField($field)
    {
        return $this->{$field} ?? null;
    }
}
