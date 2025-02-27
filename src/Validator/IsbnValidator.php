<?php

namespace App\Validator;

class ISBNValidator
{
    private const LONG_ISBN_LENGTH = 13;
    private const SHORT_ISBN_LENGTH = 10;
    private const LONG_ISBN_DIVIDER = 10;
    private const SHORT_ISBN_DIVIDER = 11;

    public static function validate(string $isbn): bool
    {
        if (strlen($isbn) === self::LONG_ISBN_LENGTH) {
            return self::validateLongISBN($isbn);
        } elseif (strlen($isbn) === self::SHORT_ISBN_LENGTH) {
            return self::validateShortISBN($isbn);
        }

        throw new \InvalidArgumentException("Un ISBN doit contenir 10 ou 13 chiffres.");
    }

    private static function validateLongISBN(string $isbn): bool
    {
        if (!ctype_digit($isbn)) {
            throw new \InvalidArgumentException("Un ISBN doit contenir uniquement des chiffres.");
        }

        $total = 0;
        for ($i = 0; $i < self::LONG_ISBN_LENGTH; $i++) {
            $num = (int) $isbn[$i];
            $total += ($i % 2 === 0) ? $num : $num * 3;
        }

        return $total % self::LONG_ISBN_DIVIDER === 0;
    }

    private static function validateShortISBN(string $isbn): bool
    {
        $total = 0;
        for ($i = 0; $i < self::SHORT_ISBN_LENGTH; $i++) {
            if (!ctype_digit($isbn[$i]) && !($i === 9 && $isbn[$i] === 'X')) {
                throw new \InvalidArgumentException("Un ISBN doit contenir uniquement des chiffres.");
            }

            $num = ($isbn[$i] === 'X') ? 10 : (int) $isbn[$i];
            $total += $num * (10 - $i);
        }

        return $total % self::SHORT_ISBN_DIVIDER === 0;
    }
}
