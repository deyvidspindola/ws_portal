<?php

/**
 * View helpers da FinRescisуo
 * Funчѕes utilizadas na exibiчуo de dados
 */
 
/**
 * Formata um valor numщrico em formato monetсrio (999.999,99)
 * @param   float|int   $value
 * @return  string
 */
function toMoney($value)
{
    ob_start();
    echo (float) $value;
    ob_clean();    
    
    return number_format((float) $value, 2, ',', '.');
}