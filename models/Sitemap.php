<?php

namespace smart\sitemap\models;

use smart\db\ActiveRecord;

class Sitemap extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sitemap';
    }

    /**
     * Owner setter
     * @param yii\db\ActiveRecord $owner owner object
     * @return void
     */
    public function setOwner($owner)
    {
        $this->ownerKey = self::generateOwnerString($owner);
    }

    /**
     * Find by owner
     * @param yii\db\ActiveRecord $owner owner object
     * @return static|null
     */
    public static function findByOwner($owner)
    {
        return static::findOne([
            'ownerKey' => self::generateOwnerString($owner),
        ]);
    }

    /**
     * Owner string generator
     * @param ActiveRecord $owner 
     * @return string
     */
    private static function generateOwnerString($owner)
    {
        $result = $owner::className();
        $result .= ':' . serialize($owner->getPrimaryKey());

        return $result;
    }
}
