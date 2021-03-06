<?php

namespace LaravelEnso\VueDatatable\app\Classes\Template\Builders;

use LaravelEnso\Helpers\app\Classes\Obj;
use LaravelEnso\VueDatatable\app\Classes\Attributes\Column as Attributes;

class Columns
{
    private $template;
    private $meta;

    public function __construct(Obj $template, Obj $meta)
    {
        $this->template = $template;
        $this->meta = $meta;
    }

    public function build()
    {
        $columns = collect($this->template->get('columns'))
            ->reduce(function ($columns, $column) {
                $this->computeMeta($column)
                    ->computeDefaultSort($column)
                    ->updateDefaults($column);
                $columns->push($column);

                return $columns;
            }, collect());

        $this->template->set('columns', $columns);
    }

    private function computeMeta($column)
    {
        if (! $column->has('meta')) {
            $column->set('meta', []);
        }

        $meta = collect(Attributes::Meta)
            ->reduce(function ($meta, $attribute) use ($column) {
                $meta->set($attribute, collect($column->meta)->contains($attribute));

                return $meta;
            }, new Obj);

        $meta->set('visible', true);
        $meta->set('hidden', false);
        $column->set('meta', $meta);

        return $this;
    }

    private function computeDefaultSort($column)
    {
        $meta = $column->get('meta');

        if ($meta->get('sort:ASC')) {
            $meta->set('sort', 'ASC');
        } elseif ($meta->get('sort:DESC')) {
            $meta->set('sort', 'DESC');
        }

        if (! $meta->has('sort')) {
            $meta->set('sort', null);
        }

        $meta->forget(['sort:ASC', 'sort:DESC']);

        return $this;
    }

    private function updateDefaults($column)
    {
        $meta = $column->get('meta');

        if ($meta->get('searchable')) {
            $this->meta->set('searchable', true);
        }

        if ($meta->get('total') || $meta->get('customTotal')) {
            $this->meta->set('total', true);
        }

        if ($meta->get('date')) {
            $this->meta->set('date', true);
        }

        if ($meta->get('translatable')) {
            $this->meta->set('translatable', true);
        }

        if ($column->has('enum')) {
            $this->meta->set('enum', true);
        }

        if ($column->has('money')) {
            $this->meta->set('money', true);
        }
    }
}
