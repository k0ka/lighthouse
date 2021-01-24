<?php

namespace Nuwave\Lighthouse\Schema\Directives;

use Exception;
use Laravel\Scout\Builder as ScoutBuilder;
use Nuwave\Lighthouse\Support\Contracts\ArgBuilderDirective;

class NotInDirective extends BaseDirective implements ArgBuilderDirective
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
Use the client given value to add a NOT IN conditional to a database query.
"""
directive @notIn(
  """
  Specify the database column to compare.
  Only required if database column has a different name than the attribute in your schema.
  """
  key: String
) repeatable on ARGUMENT_DEFINITION | INPUT_FIELD_DEFINITION
GRAPHQL;
    }

    /**
     * Apply a simple "WHERE NOT IN $values" clause.
     */
    public function handleBuilder($builder, $values): object
    {
        if ($builder instanceof ScoutBuilder) {
            throw new Exception("Using {$this->name()} on queries that use a Scout search is not supported.");
        }

        return $builder->whereNotIn(
            $this->directiveArgValue('key', $this->nodeName()),
            $values
        );
    }
}
