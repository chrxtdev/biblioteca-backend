<?php

namespace App\Enums;

enum Course: string
{
    case Engenharias = 'Engenharias';
    case CienciasHumanas = 'Ciências Humanas e Sociais';
    case Saude = 'Área da Saúde';
    case Tecnologia = 'Tecnologia e TI';
    case Geral = 'Conteúdos Gerais';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_combine(self::values(), self::values());
    }
}
