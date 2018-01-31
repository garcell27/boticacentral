<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\widgets;

use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class AceGridView extends GridView{

    const TYPE_DEFAULT = '';
    /**
     * The **primary** bootstrap contextual color type
     */
    const TYPE_PRIMARY = 'color-grey';
    /**
     * The **information** bootstrap contextual color type
     */
    const TYPE_INFO = 'color-blue';
    /**
     * The **danger** bootstrap contextual color type
     */
    const TYPE_DANGER = 'color-red';
    /**
     * The **warning** bootstrap contextual color type
     */
    const TYPE_WARNING = 'color-orange';
    /**
     * The **success** bootstrap contextual color type
     */
    const TYPE_SUCCESS = 'color-green';
    /**
     * The **active** bootstrap contextual color type (applicable only for table row contextual style)
     */
    const TYPE_ACTIVE = 'active';

    public $panelPrefix = 'widget-box widget-';

    public $panelTemplate = <<< HTML
<div class="{prefix}{type}">
    {panelHeading}
    <div class="widget-body">
        <div class="widget-main no-padding">
            {panelBefore}
            {items}
            {panelAfter}
        </div>
        {panelFooter}
    </div>
</div>
HTML;

    public $panelHeadingTemplate = <<< HTML
    <h4 class="widget-title">
        {heading}
    </h4>
    {toolbarContainer}
    <div class="clearfix"></div>
HTML;

    public $panelFooterTemplate = <<< HTML
    <div class="kv-panel-pager">
        <div class="col-md-6">
             {summary}
        </div>
        <div class="col-md-6">
            {pager}
        </div>

    </div>
    {footer}
HTML;

    public $panelBeforeTemplate = <<< HTML
    {before}
HTML;


    public $toolbarContainerOptions = ['class' => 'widget-toolbar'];
    protected function renderPanel()
    {
        if (!$this->bootstrap || !is_array($this->panel) || empty($this->panel)) {
            return;
        }
        $type = ArrayHelper::getValue($this->panel, 'type', 'default');
        $heading = ArrayHelper::getValue($this->panel, 'heading', '');
        $footer = ArrayHelper::getValue($this->panel, 'footer', '');
        $before = ArrayHelper::getValue($this->panel, 'before', '');
        $after = ArrayHelper::getValue($this->panel, 'after', '');
        $headingOptions = ArrayHelper::getValue($this->panel, 'headingOptions', []);
        $footerOptions = ArrayHelper::getValue($this->panel, 'footerOptions', []);
        $beforeOptions = ArrayHelper::getValue($this->panel, 'beforeOptions', []);
        $afterOptions = ArrayHelper::getValue($this->panel, 'afterOptions', []);
        $panelHeading = '';
        $panelBefore = '';
        $panelAfter = '';
        $panelFooter = '';

        if ($heading !== false) {
            static::initCss($headingOptions, 'widget-header');
            $content = strtr($this->panelHeadingTemplate, ['{heading}' => $heading]);
            $panelHeading = Html::tag('div', $content, $headingOptions);
        }
        if ($footer !== false) {
            static::initCss($footerOptions, 'widget-toolbox padding-8 clearfix');
            $content = strtr($this->panelFooterTemplate, ['{footer}' => $footer]);
            $panelFooter = Html::tag('div', $content, $footerOptions);
        }
        if ($before !== false) {
            static::initCss($beforeOptions, '');
            $content = strtr($this->panelBeforeTemplate, ['{before}' => $before]);
            $panelBefore = Html::tag('div', $content, $beforeOptions);
        }
        if ($after !== false) {
            static::initCss($afterOptions, '');
            $content = strtr($this->panelAfterTemplate, ['{after}' => $after]);
            $panelAfter = Html::tag('div', $content, $afterOptions);
        }
        $this->layout = strtr(
            $this->panelTemplate,
            [
                '{panelHeading}' => $panelHeading,
                '{prefix}' => $this->panelPrefix,
                '{type}' => $type,
                '{panelFooter}' => $panelFooter,
                '{panelBefore}' => $panelBefore,
                '{panelAfter}' => $panelAfter,
            ]
        );
    }

}