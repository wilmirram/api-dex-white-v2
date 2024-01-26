<?php


namespace App\Utils;


use DB;

class GenericsMedsSheet extends Sheet
{
    public function render($farmacyName = null)
    {
        $renderedSheet = [];
        if(!$this->verifyIntegrity()) return ['ERROR' => true, 'DATA' => 'PLANILHA INVALIDA'];
        foreach ($this->inArray as $sheet) {
            if ($sheet[2] != null || $sheet[2] != '') {
                if($sheet[1] !== 'Código EAN' &&  $sheet[8] !== 'Categoria'){
                    $formatedPrices = $this->formatPrices($sheet[4], $sheet[5], $sheet[6]);
                    if (!$formatedPrices) return ['ERROR' => true, 'DATA' => 'PREÇOS INVALIDOS'];
                    if ($formatedPrices['precoFabrica'] == 0.0 || $formatedPrices['precoFabrica'] == 00.00) return ['ERROR' => true, 'DATA' => 'VOCE NAO PODE INSERIR UM PRODUTO SEM PREÇO DE FABRICA - ('. $sheet[1] . ')'];
                    if ($formatedPrices['precoFabrica'] > $formatedPrices['pmc']) return ['ERROR' => true, 'DATA' => 'VOCE NAO PODE INSERIR UM PRODUTO COM PREÇO DE FABRICA MAIOR QUE O DE VENDA  - ('. $sheet[1] . ')'];
                    //if ($formatedPrices['precoFabrica'] > $formatedPrices['pmc']) return ['ERROR' => true, 'DATA' => $formatedPrices];
                    if ($formatedPrices['pmc'] == 0.0 || $formatedPrices['pmc'] == 00.00) return ['ERROR' => true, 'DATA' => 'VOCE NAO PODE INSERIR UM PRODUTO SEM PREÇO DE VENDA - ('. $sheet[1] . ')'];
                    array_push($renderedSheet, [
                        'INTERNAL_VENDOR_CODE' => $sheet[0],
                        'REFERENCE_CODE' => $sheet[1],
                        'NAME' => $sheet[2],
                        'BRAND' => $sheet[3],
                        'FACTORY_PRICE' => $formatedPrices['precoFabrica'],
                        'SALE_PRICE' => $formatedPrices['pmc'],
                        'GROUP_DESCRIPTION' => $sheet[8],
                        'GROUP' => $sheet[9],
                        'VS_SCORING_RULE_ID' => $sheet[10]
                    ]);
                }
            }
        }
        return ['ERROR' => false, 'DATA' => $renderedSheet];
    }

    public function verifyIntegrity()
    {
        $avaliable = [
            'Código EAN' => 1,
            'Descrição' => 2,
            'Laboratório' => 3,
            'Preço Fábrica' => 4,
            'PMC' => 5,
            'Desc. Com.' => 6,
            'Tipo Lista' => 7,
            'Categoria' => 8,
            'Grupo' => 9
        ];

        foreach ($avaliable as $key => $item) {
            if ($key != $this->inArray[3][$item]) return false;
        }
        return true;
    }

    private function formatPrices($precoFabrica, $pmc, $descCom)
    {
        $formatedPrices = [];
        try {
            $precoFabrica = str_replace([',', 'R$', ' '], ['.', '', ''], $precoFabrica);
            $formatedPrices['precoFabrica'] = (double) $precoFabrica;

            if ($formatedPrices['precoFabrica'] == 0.0){
                $precoFabrica = json_encode($precoFabrica);
                $precoFabrica = str_replace('\u00a0', '',$precoFabrica);
                $precoFabrica = json_decode($precoFabrica);

                $formatedPrices['precoFabrica'] = (double) $precoFabrica;
            }

            $pmc = str_replace([',', 'R$', ' ', '�', 'Â'], ['.', '', '', '', ''], $pmc);
            $formatedPrices['pmc'] = (double) $pmc;
            if($formatedPrices['pmc'] == 0.0) {
                $pmc = json_encode($pmc);
                $pmc = str_replace('\u00a0', '',$pmc);
                $pmc = json_decode($pmc);

                $formatedPrices['pmc'] = (double) $pmc;
            };

            $descCom = str_replace([',', 'R$', ' '], ['.', '', ''], $descCom[0]);
            $formatedPrices['descCom'] = (double) $descCom;
            if ($formatedPrices['descCom'] == 0.0){
                $descCom = json_encode($descCom);
                $descCom = str_replace('\u00a0', '',$descCom);
                $descCom = json_decode($descCom);

                $formatedPrices['descCom'] = (double) $descCom;
            }
        }catch (\Exception $e){
            return false;
        }

        return $formatedPrices;
    }

    public function getDrugsInDatabase($referenceCodes)
    {
        try {
            $result = DB::select("
                        SELECT  VP.INTERNAL_VENDOR_CODE,
        VP.REFERENCE_CODE,
        VP.NAME,
        VB.NAME AS BRAND,
        VPP.UNIT_PRICE AS FACTORY_PRICE,
        VPP.SALE_PRICE,
        VGD.DESCRIPTION AS GROUP_DESCRIPTION,
        VPP.VS_GROUP_OF_DRUG_ID AS `GROUP`,
        VP.VS_SCORING_RULE_ID
	FROM VS_PRODUCT VP
	JOIN VS_BRAND VB
	  ON VP.VS_BRAND_ID = VB.ID
	JOIN VS_PRODUCT_PRICE VPP
	  ON VP.ID = VPP.VS_PRODUCT_ID
	LEFT JOIN VS_GROUP_OF_DRUG VGD
	  ON VPP.VS_GROUP_OF_DRUG_ID = VGD.ID

	WHERE VPP.ID = ( SELECT MAX(RES.ID ) FROM VS_PRODUCT_PRICE RES WHERE RES.VS_PRODUCT_ID = VP.ID)
	  AND VP.REFERENCE_CODE IN ({$referenceCodes})");
            return $result;
        }catch (\Exception $e){
            return 'false';
        }
    }
}
