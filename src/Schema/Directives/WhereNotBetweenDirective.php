<?php

namespace Nuwave\Lighthouse\Schema\Directives;

use Exception;
use Laravel\Scout\Builder as ScoutBuilder;
use Nuwave\Lighthouse\Support\Contracts\ArgBuilderDirective;

class WhereNotBetweenDirective extends BaseDirective implements ArgBuilderDirective
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
Verify that a column's value lies outside of two values.

The type of the input value this is defined upon should be
an `input` object with two fields.
"""
directive @whereNotBetween(
  """
  Specify the database column to compare.
  Only required if database column has a different name than the attribute in your schema.
  """
  key: String
) repeatable on ARGUMENT_DEFINITION | INPUT_FIELD_DEFINITION
GRAPHQL;
    }

    /**
     * Apply a "WHERE NOT BETWEEN" clause.
     */
    public function handleBuilder($builder, $values): object
    {
        if ($builder instanceof ScoutBuilder) {
            throw new Exception("Using {$this->name()} on queries that use a Scout search is not supported.");
        }

        return $builder->whereNotBetween(
            $this->directiveArgValue('key', $this->nodeName()),
            $values
        );
    }
}
