<?php

/* @var $this yii\web\View */

/** @var \menst\cms\common\models\MenuItem $menu */
$menu = Yii::$app->menuManager->getActiveMenu();
if ($menu) {
    $this->title = $menu->isProperContext() ? $menu->title : Yii::t('menst.cms', 'Contact');
    $this->params['breadcrumbs'] = $menu->getBreadcrumbs($menu->isApplicableContext());
} else {
    $this->title = Yii::t('menst.cms', 'Contact');
}

echo \menst\cms\frontend\widgets\Contact::widget([
    'id' => 'contact'
]);

