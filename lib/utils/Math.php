<?php
/*
    Class Math
    Essa classe tem o objetivo de concentrar funções matemáticas usadas
    no sistema.
    
    -----------------------------------------------
        COPYRIGHT
    -----------------------------------------------
    
    -----------------------------------------------
        HOW TO USE
    -----------------------------------------------
        //Inclua a class no arquivo
        require 'lib/Math.php';
        
        //Chame as funções normalmente
        echo Math::Mod11('261533'); //Retorna 9
        echo Math::Mod10('261533'); //Retorna 4
        
    -----------------------------------------------
        CHANGELOG
    -----------------------------------------------
    
    17/05/2011
    [+] Mod11() calcula o módulo 11 do número informado
    [+] Mod10() calcula o módulo 10 do número informado
    [+] financi() calcula a prestacao de um financiamento considerando
        periodo, valor inicial e juros
    
    -----------------------------------------------
        TO DO
    -----------------------------------------------
    
    - Cálculo de parcelas em um parcelamento simples, com arredondamento,
    retirada dos centavos para outra prestação, etc.
    
    -----------------------------------------------
        KNOWN BUGS
    -----------------------------------------------
    Ao passar números como inteiros, e que comecem com 0, o PHP
    entendará tratar-se de um octal e o cálculo será errado. A solução
    é sempre evitar adicionar à esquerda do número, ou sempre passar
    o número entre aspas, mantendo como string e evitando a conversão
    automática para octal.
    
    
 */
class Math{
    
    /**
      * method Mod11()
      * Calcula o módulo 11 de um número, conforme esquema abaixo, retirado
      * de http://pt.wikipedia.org/wiki/D%C3%ADgito_verificador
      
            +---+---+---+---+---+---+   +---+
            | 2 | 6 | 1 | 5 | 3 | 3 | - | 9 |<---
            +---+---+---+---+---+---+   +---+
              |   |   |   |   |   |
              x7  x6  x5  x4  x3  x2
              |   |   |   |   |   |
              =14 =36 =5  =20 =9  =6
              +---+---+---+---+---+-> = (90 x 10) / 11 = 81, resto 9 => Dígito = 9
          
            +---+---+---+---+---+---+   +---+---+
            | 2 | 6 | 1 | 5 | 3 | 3 | - | 9 | 4 |<---
            +---+---+---+---+---+---+   +---+---+
              |   |   |   |   |   |       |
              x8  x7  x6  x5  x4  x3      x2
              |   |   |   |   |   |       |
              =16 =42 =6  =25 =12 =9      =18
              +---+---+---+---+---+-> = (128 x 10) / 11 = 116, resto 4 => Dígito = 4
      
      * @version 0.1 17/05/2011 Initial
      *
      * @param $number O número informado
      * @param $ifTen Caso o resto da divisão seja 10, o que colocar
      *     em seu lugar? Existem exemplos de bancos que adicionam
      *     "0", outros "1", outros "X", outros "P", etc
      * @param $ifZero Se o resultado for zero, substituir por algum outro valor?
      * @return mixed
      */
    public static function Mod11($number, $ifTen = '0', $ifZero = '0'){
        $numLen = strlen($number) - 1;
        $sum = 0;
        $factor = 2;
        
        for($i = $numLen; $i >= 0; $i --){
            $sum += substr($number, $i, 1) * $factor;
            //pr($factor);
            $factor = $factor >= 9 ? 2 : $factor + 1;
        }
        //Resto da divisão
        $rest = ($sum * 10) % 11;
        //ifTen
        $rest = $rest == 10 ? $ifTen : $rest;
        //ifZero
        $rest = $rest === 0 ? $ifZero : $rest;
        
        switch($rest){
            case 10: $ifTen;  break;
            case 0:  $ifZero; break;
            default: $rest;   break;
        }
        
        return $rest;
    }
    
    
    /**
      * Method Mod10()
      * Calcula o módulo 10 de um número, conforme o esquema a seguir
      * 
        +---+---+---+---+---+---+   +---+
        | 2 | 6 | 1 | 5 | 3 | 3 | - | 4 |
        +---+---+---+---+---+---+   +---+
          |   |   |   |   |   |
         x1  x2  x1  x2  x1  x2
          |   |   |   |   |   |
         =2 =12  =1 =10  =3  =6
         +2 +1+2 +1 +1+0 +3  +6 = 16
        +---+---+---+---+---+-> = (16 / 10) = 1, resto 6 => DV = (10 - 6) = 4
        *Se o resto for diferente de 0, o resultado será 10 menos esse número
      *
      * @param $number Número a ser calculado o módulo 10
      * @return integer
      */
    public static function Mod10($number){
        $numLen = strlen($number) - 1;
        $sum = 0;
        $factor = 2;
        
        for($i = $numLen; $i >= 0; $i --){
            $result = substr($number, $i, 1) * $factor;
            
            if($result >= 10){
                $result = substr($result, 0, 1) + substr($result, 1, 1);
            }
            
            $sum += $result;
            $factor = $factor == 2 ? 1 : 2;
        }
        //Resto da divisão
        $rest = $sum % 10;
        
        return $rest <> 0 ? 10 - $rest : $rest;
    }
    
    /**
      * http://pt.wikipedia.org/wiki/Tabela_price#C.C3.A1lculo
      */
    public static function financing($investimento, $juros, $periodo) {
        return ($investimento * $juros) / (1 - (1 / pow((1 + $juros), $periodo)));
    }
}