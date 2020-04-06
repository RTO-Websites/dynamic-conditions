<?php
/**
 * @var array $settings
 * @var string $dynamicTagValue
 * @var string $dynamicTagValueRaw
 * @var string $checkValue
 * @var string $checkValue2
 * @var string $visibility
 * @var bool $conditionMets
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

?>

<div class="dynamicconditions-debug">
    <div class="dc-debug-row">
        <div class="dc-debug-label">Element:</div>
        <div class="dc-debug-value"><?php
            echo $settings['dynamicConditionsData']['name'] . '-'
                . $settings['dynamicConditionsData']['id']
                . ' (' . $settings['dynamicConditionsData']['type'] . ')'; ?></div>
    </div>

    <div class="dc-debug-row">
        <div class="dc-debug-label">DynamicTag-Tag:</div>
        <div class="dc-debug-value"><?php echo $settings['dynamicConditionsData']['selectedTag']; ?></div>
    </div>

    <div class="dc-debug-row">
        <div class="dc-debug-label">DynamicTag-Key:</div>
        <div class="dc-debug-value"><?php echo $settings['dynamicConditionsData']['tagKey']; ?></div>
    </div>

    <div class="dc-debug-row">
        <div class="dc-debug-label">DynamicTag-Value:</div>
        <div class="dc-debug-value"><?php echo $dynamicTagValue; ?></div>
    </div>
    <div class="dc-debug-row">
        <div class="dc-debug-label">DynamicTag-Value-Raw:</div>
        <div class="dc-debug-value"><?php echo $dynamicTagValueRaw; ?></div>
    </div>

    <div class="dc-debug-row">
        <div class="dc-debug-label">Check-Value:</div>
        <div class="dc-debug-value"><?php echo $checkValue; ?></div>
    </div>

    <div class="dc-debug-row">
        <div class="dc-debug-label">Check-Value2:</div>
        <div class="dc-debug-value"><?php echo $checkValue2; ?></div>
    </div>

    <div class="dc-debug-row">
        <div class="dc-debug-label">Condition-Type:</div>
        <div class="dc-debug-value"><?php echo $settings['dynamicconditions_type']; ?></div>
    </div>

    <div class="dc-debug-row">
        <div class="dc-debug-label">Condition:</div>
        <div class="dc-debug-value"><?php echo ucfirst( $visibility ) . ' if ' . $settings['dynamicconditions_condition']; ?></div>
    </div>

    <div class="dc-debug-row">
        <div class="dc-debug-label">Condition met:</div>
        <div class="dc-debug-value"><?php echo $conditionMets ? 'yes' : 'no'; ?></div>
    </div>

    <i class="fa fa-window-close dc-debug-remove" aria-hidden="true" onclick="this.parentNode.remove();"></i>

</div>