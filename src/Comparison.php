<?php

namespace ErikKubica\ArrayLib;

enum Comparison: string
{
    case EQ = '=';
    case NE = '!=';
    case GT = '>';
    case GE = '>=';
    case LT = '<';
    case LE = '<=';
}