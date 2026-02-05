<?php 

namespace App\Services;

trait HelperEvaluateSumExcel
{
    function evaluateSumFormula(string $formula, array $data): float
    {
        $formula = trim($formula);

        // Hilangkan "=" di depan
        if (str_starts_with($formula, '=')) {
            $formula = substr($formula, 1);
        }

        // =========================
        // IFERROR(expr, fallback)
        // =========================
        if (preg_match('/^IFERROR\((.+),(.+)\)$/i', $formula, $m)) {
            try {
                $value = $this->evaluateSumFormula('=' . trim($m[1]), $data);

                if (!is_finite($value)) {
                    throw new \Exception('Invalid result');
                }

                return $value;
            } catch (\Throwable $e) {
                return (float) $this->evaluateSumFormula('=' . trim($m[2]), $data);
            }
        }

        // === CASE 1: Cell reference langsung (E17)
        if (preg_match('/^[A-Z]+\d+$/', $formula)) {
            $value = $data[$formula] ?? 0;

            if (is_string($value) && str_starts_with($value, '=')) {
                return $this->evaluateSumFormula($value, $data);
            }

            return (float) $value;
        }

        // === CASE 2: SUM(...)
        if (preg_match('/^SUM\((.*)\)$/i', $formula, $matches)) {
            $sum = 0.0;

            // split by comma (level pertama saja)
            $parts = array_map('trim', explode(',', $matches[1]));

            foreach ($parts as $part) {

                // RANGE: E1:E14
                if (str_contains($part, ':')) {
                    [$start, $end] = explode(':', $part);

                    preg_match('/([A-Z]+)(\d+)/', $start, $s);
                    preg_match('/([A-Z]+)(\d+)/', $end, $e);

                    for ($col = ord($s[1]); $col <= ord($e[1]); $col++) {
                        for ($row = (int)$s[2]; $row <= (int)$e[2]; $row++) {
                            $cell = chr($col) . $row;
                            $sum += $this->evaluateSumFormula('=' . ($data[$cell] ?? 0), $data);
                        }
                    }
                }

                // SINGLE CELL / EXPRESSION: E14, E16, E19, E14+E16
                else {
                    $sum += $this->evaluateSumFormula('=' . $part, $data);
                }
            }

            return $sum;
        }

        // =========================
        // Operator * dan /
        // =========================
        if (preg_match('/(.+)([\/\*])(.+)/', $formula, $m)) {

            $left  = $this->evaluateSumFormula('=' . trim($m[1]), $data);
            $right = $this->evaluateSumFormula('=' . trim($m[3]), $data);

            // MULTIPLY
            if ($m[2] === '*') {
                return $left * $right;
            }

            // DIVIDE
            if ($m[2] === '/') {

                // ⛔ kondisi error Excel
                if ($right == 0 || !is_finite($right)) {
                    throw new \DivisionByZeroError('Division by zero');
                }

                return $left / $right;
            }
        }

        // === CASE 3: Expression dengan + dan -
        // contoh: E12 - E14 + SUM(E15:E16)
        preg_match_all('/([+-]?[^+-]+)/', $formula, $tokens);

        $result = 0.0;

        foreach ($tokens[0] as $token) {
            $token = trim($token);

            $operator = '+';
            if ($token[0] === '+' || $token[0] === '-') {
                $operator = $token[0];
                $token = trim(substr($token, 1));
            }

            $value = 0.0;

            // RANGE
            if (str_contains($token, ':')) {
                [$start, $end] = explode(':', $token);

                preg_match('/([A-Z]+)(\d+)/', $start, $s);
                preg_match('/([A-Z]+)(\d+)/', $end, $e);

                for ($col = ord($s[1]); $col <= ord($e[1]); $col++) {
                    for ($row = (int)$s[2]; $row <= (int)$e[2]; $row++) {
                        $cell = chr($col) . $row;
                        $cellValue = $data[$cell] ?? 0;

                        $value += is_string($cellValue) && str_starts_with($cellValue, '=')
                            ? $this->evaluateSumFormula($cellValue, $data)
                            : (float) $cellValue;
                    }
                }
            }
            // SINGLE CELL / NESTED FORMULA
            else {
                $cellValue = $data[$token] ?? $token;

                $value = is_string($cellValue) && str_starts_with($cellValue, '=')
                    ? $this->evaluateSumFormula($cellValue, $data)
                    : (float) $cellValue;
            }

            $result = $operator === '-' ? $result - $value : $result + $value;
        }

        return $result;
    }


}