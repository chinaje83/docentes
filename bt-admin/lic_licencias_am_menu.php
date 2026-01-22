<?php

if (!isset($sesion)) die();

switch($stepCurrent)
{
    case 1:
        $step1 = "current";
        $step2 = "disabled";
        $step3 = "disabled";
        $step4 = "disabled";
        break;
    case 2:
        $step1 = "done";
        $step2 = "current";
        $step3 = "disabled";
        $step4 = "disabled";
        break;
    case 3:
        $step1 = "done";
        $step2 = "done";
        $step3 = "current";
        $step4 = "disabled";
        break;
    case 4:
        $step1 = "done";
        $step2 = "done";
        $step3 = "done";
        $step4 = "current";
        break;

}
?>
<div class="wizard-content d-none d-md-block">
    <div class="tab-wizard wizard-circle wizard clearfix">
        <div class="steps clearfix">
            <ul role="tablist">
                <li role="tab" class="first <?php echo $step1?>" aria-disabled="false" aria-selected="true">
                    <a id="steps-uid-0-t-0" href="javascript:void(0)" aria-controls="steps-uid-0-p-0">
                        <span class="step">1</span> Buscar persona
                    </a>
                </li>
                <li role="tab" class=" <?php echo $step2?>" aria-disabled="false" aria-selected="false">
                    <a id="steps-uid-0-t-1" href="javascript:void(0)" aria-controls="steps-uid-0-p-1">
                        <span class="step">2</span> Completar datos
                    </a>
                </li>
                <li role="tab" class=" <?php echo $step3?>" aria-disabled="false" aria-selected="false">
                    <a id="steps-uid-0-t-2" href="javascript:void(0)" aria-controls="steps-uid-0-p-2">
                        <span class="step">3</span> Pendiente de Revisi&oacute;n
                    </a>
                </li>
                <li role="tab" class=" <?php echo $step4?> last" aria-disabled="false" aria-selected="false">
                    <a id="steps-uid-0-t-2" href="javascript:void(0)" aria-controls="steps-uid-0-p-2">
                        <span class="step">4</span> Finalizada
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
