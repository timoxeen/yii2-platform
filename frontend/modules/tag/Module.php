<?php
/**
 * @link https://github.com/gromver/yii2-cmf.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-cmf/blob/master/LICENSE
 * @package yii2-cmf
 * @version 1.0.0
 */

namespace gromver\cmf\frontend\modules\tag;

use gromver\cmf\frontend\interfaces\MenuRouterInterface;
use gromver\cmf\frontend\modules\tag\components\MenuRouterTag;

/**
 * Class Module
 * @package yii2-cmf
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements MenuRouterInterface
{
    public $controllerNamespace = 'gromver\cmf\frontend\modules\tag\controllers';

    /**
     * @inheritdoc
     */
    public function getMenuRouter()
    {
        return MenuRouterTag::className();
    }
}
