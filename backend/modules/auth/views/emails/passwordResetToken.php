<?php
/**
 * @var yii\web\View $this
 * @var menst\cms\common\models\User $user
 */

echo Yii::t('menst.cms', 'For change of the password follow the <a href="{link}">link</a>', ['link' => \yii\helpers\Url::toRoute(['/cms/auth/default/reset-password', 'token' => $user->password_reset_token], true)]);