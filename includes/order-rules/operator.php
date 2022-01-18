<?php

if (!class_exists('TRUNKRS_WC_RuleOperator')) {
  class TRUNKRS_WC_RuleOperator
  {
    public const EQUALS = 'EQ';
    public const NOT_EQUALS = 'NQ';

    public const CONTAINS = 'CO';

    public const STARTS_WITH = 'SW';
    public const ENDS_WITH = 'EW';
  }
}

