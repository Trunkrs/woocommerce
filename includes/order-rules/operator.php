<?php

if (!class_exists('TRUNKRS_WC_RuleOperator')) {
  class TRUNKRS_WC_RuleOperator
  {
    public const EQUALS = 'EQ';
    public const NOT_EQUALS = 'NQ';

    public const GREATER_THAN = 'GT';
    public const LOWER_THAN = 'LT';

    public const CONTAINS = 'CO';

    public const STARTS_WITH = 'SW';
    public const ENDS_WITH = 'EW';
  }
}

