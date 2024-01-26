<?php


namespace App\Utils;


class DiscountSheet extends Sheet
{
    public function render($param = null)
    {
        $renderedSheet = [];
        foreach ($this->inArray as $sheet) {
            if ($sheet[3] != null || $sheet[3] != '') {
                foreach ($sheet as $key => $value){
                    if ($value == '-') $sheet[$key] = 0;
                }
                if (is_string($sheet[5])) $sheet[5] = str_replace([',', '.'], ['', ','], $sheet[5]);
                array_push($renderedSheet, [
                    'grupo' => (int) $sheet[0],
                    'subGrupo' => (int) $sheet[1],
                    'descricao' => $sheet[3],
                    'fxInicio' => $sheet[4],
                    'fxFinal' => (double) $sheet[5],
                    'lista1' => $sheet[6],
                    'lista2' => $sheet[7],
                    'lista3' => $sheet[8],
                    'lista4' => $sheet[9],
                    'lista5' => $sheet[10],
                    'lista6' => $sheet[11],
                    'lista7' => $sheet[12],
                    'lista8' => $sheet[13],
                    'lista9' => $sheet[14],
                    'lista10' => $sheet[15],
                    'listaAv' => $sheet[16],
                    'listaAp' => $sheet[17],
                ]);
            }
        }

        return $renderedSheet;
    }

    protected function verifyIntegrity()
    {
        // TODO: Implement verifyIntegrity() method.
    }
}
