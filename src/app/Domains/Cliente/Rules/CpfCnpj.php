<?php

namespace App\Domains\Cliente\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfCnpj implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $documentoNumerico = preg_replace('/[^0-9]/', '', (string) $value);

        if (strlen($documentoNumerico) === 11) {
            if (!$this->validaCPF($documentoNumerico)) {
                $fail('O CPF digitado não é um CPF válido.');
            }
        } elseif (strlen($documentoNumerico) === 14) {
            if (!$this->validaCNPJ($documentoNumerico)) {
                $fail('O CNPJ digitado não é um CNPJ válido.');
            }
        } else {
            $fail('O :attribute deve conter 11 (CPF) ou 14 (CNPJ) dígitos. Você digitou ' . strlen($documentoNumerico) . ' dígitos.');
        }
    }

    private function validaCPF(string $cpf): bool
    {
        // Verifica se é uma sequência repetida ex: 11111111111
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($tamanhoSendoValidado = 9; $tamanhoSendoValidado < 11; $tamanhoSendoValidado++) {
            $somaCalculada = 0;

            for ($indiceComposicao = 0; $indiceComposicao < $tamanhoSendoValidado; $indiceComposicao++) {
                $algarismoBase = $cpf[$indiceComposicao];
                $pesoDecrementado = (($tamanhoSendoValidado + 1) - $indiceComposicao);

                $somaCalculada += $algarismoBase * $pesoDecrementado;
            }

            $digitoEncontradoMatematicamente = ((10 * $somaCalculada) % 11) % 10;
            $digitoApresentadoNoCpf = $cpf[$tamanhoSendoValidado];

            if ($digitoApresentadoNoCpf != $digitoEncontradoMatematicamente) {
                return false;
            }
        }

        return true;
    }

    private function validaCNPJ(string $cnpj): bool
    {
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        $pesquisosDoPrimeiroDigito = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $somaParaAcharZeroUm = 0;

        for ($indiceCaractere = 0; $indiceCaractere < 12; $indiceCaractere++) {
            $somaParaAcharZeroUm += $cnpj[$indiceCaractere] * $pesquisosDoPrimeiroDigito[$indiceCaractere];
        }

        $restoPrimeiraConta = $somaParaAcharZeroUm % 11;
        $primeiroDigitoQueDeveBater = ($restoPrimeiraConta < 2) ? 0 : (11 - $restoPrimeiraConta);

        if ($cnpj[12] != $primeiroDigitoQueDeveBater) {
            return false;
        }

        $pesquisasDoSegundoDigito = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $somaParaAcharZeroDois = 0;

        for ($indiceCaractereSegundo = 0; $indiceCaractereSegundo < 13; $indiceCaractereSegundo++) {
            $somaParaAcharZeroDois += $cnpj[$indiceCaractereSegundo] * $pesquisasDoSegundoDigito[$indiceCaractereSegundo];
        }

        $restoSegundaConta = $somaParaAcharZeroDois % 11;
        $segundoDigitoQueDeveBater = ($restoSegundaConta < 2) ? 0 : (11 - $restoSegundaConta);

        if ($cnpj[13] != $segundoDigitoQueDeveBater) {
            return false;
        }

        return true;
    }
}
