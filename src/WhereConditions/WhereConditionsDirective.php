<?php

namespace Nuwave\Lighthouse\WhereConditions;

use Exception;
use Laravel\Scout\Builder as ScoutBuilder;

class WhereConditionsDirective extends WhereConditionsBaseDirective
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
Add a dynamically client-controlled WHERE condition to a fields query.
"""
directive @whereConditions(
    """
    Restrict the allowed column names to a well-defined list.
    This improves introspection capabilities and security.
    Mutually exclusive with the `columnsEnum` argument.
    """
    columns: [String!]

    """
    Use an existing enumeration type to restrict the allowed columns to a predefined list.
    This allowes you to re-use the same enum for multiple fields.
    Mutually exclusive with the `columns` argument.
    """
    columnsEnum: String
) on ARGUMENT_DEFINITION
GRAPHQL;
    }

    /**
     * @param  array<string, mixed>|null  $whereConditions
     */
    public function handleBuilder($builder, $whereConditions): object
    {
        // The value `null` should be allowed but have no effect on the query.
        if (is_null($whereConditions)) {
            return $builder;
        }

        if ($builder instanceof ScoutBuilder) {
            throw new Exception("Using {$this->name()} on queries that use a Scout search is not supported.");
        }

        return $this->handleWhereConditions($builder, $whereConditions);
    }

    protected function generatedInputSuffix(): string
    {
        return 'WhereConditions';
    }
}
