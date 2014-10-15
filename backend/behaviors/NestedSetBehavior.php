<?php
/**
 * @link https://github.com/menst/yii2-cms.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/menst/yii2-cms/blob/master/LICENSE
 * @package yii2-cms
 * @version 1.0.0
 */

namespace menst\cms\backend\behaviors;

use creocoder\behaviors\NestedSet;
use Yii;
use yii\db\Exception;
use yii\db\ActiveRecord;

/**
 * Class NestedSetBehavior
 * @package yii2-cms
 * @author Gayazov Roman <m.e.n.s.t@yandex.ru>
 */
class NestedSetBehavior extends NestedSet
{
    public $orderingAttribute = 'ordering';

    static private $_orderCache;

    /**
     * Сортировка дерева начиная с $this элемента. Сортировка поуровневая.
     *
     * @param string $orderByAttribute  Колонка по которой будет сортироватся элементы(ex: ordering asc/lft asc).
     */
    public function reorderNode($orderByAttribute, $orderByDir = SORT_ASC)
    {
        $db = $this->owner->getDb();
        $extTransFlag = $db->getTransaction();

        if ($extTransFlag === null) {
            $transaction = $db->beginTransaction();
        }

        try
        {
            self::$_orderCache = [];

            $this->applyNodeOrder([$orderByAttribute => $orderByDir], $this->owner->{$this->leftAttribute}, $this->owner->{$orderByAttribute});

            foreach (self::$_orderCache as $node) {
                /** @var $node ActiveRecord */
                $node->updateAttributes([$this->leftAttribute, $this->rightAttribute, $this->orderingAttribute]);
            }

            if($extTransFlag === null){
                $transaction->commit();
            }
        }
        catch(Exception $e)
        {
            if($extTransFlag===null){
                $transaction->rollback();
            }

            throw $e;
        }
    }
    //рекурсивная функция проходящяя по уровням дерева, применяя порядок $orderBy к выборке, с последущей нумерацией поля $this->owner->{$this->orderingAttribute}, и перестройки атрибутов lft, rgt
    /**
     * @param $orderBy array ['ordering' => 'ASC']
     * @param $leftId integer
     * @param $order integer
     * @return mixed
     */
    public function applyNodeOrder($orderBy, $leftId, $order)
    {
        $children = $this->children()->orderBy($orderBy)->all();

        // The right value of this node is the left value + 1
        $rightId = $leftId + 1;

        // Execute this function recursively over all children
        foreach ($children as $i => $node) {
            /*
             * $rightId is the current right value, which is incremented on recursion return.
             * Increment the level for the children.
             * Add this item's alias to the path (but avoid a leading /)
             */
            $rightId = $node->applyNodeOrder($orderBy, $rightId, $i+1);
        }

        $this->owner->{$this->leftAttribute} = $leftId;
        $this->owner->{$this->rightAttribute} = $rightId;
        $this->owner->{$this->orderingAttribute} = $order;

        self::$_orderCache[] = $this->owner;

        // Return the right value of this node + 1.
        return $rightId + 1;
    }
}