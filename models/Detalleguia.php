<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalleguia".
 *
 * @property integer $iddetalleguia
 * @property integer $idguiaremision
 * @property integer $idundprod
 * @property integer $idproducto
 * @property string $cantidad
 */
class Detalleguia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detalleguia';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idguiaremision', 'idundprod', 'idproducto', 'cantidad'], 'required'],
            [['idguiaremision', 'idundprod', 'idproducto'], 'integer'],
            [['cantidad'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddetalleguia' => 'Iddetalleguia',
            'idguiaremision' => 'Idguiaremision',
            'idundprod' => 'Idundprod',
            'idproducto' => 'Idproducto',
            'cantidad' => 'Cantidad',
        ];
    }
}
