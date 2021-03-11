<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "units".
 *
 * @property int $id
 * @property string|null $logo
 * @property string|null $name
 * @property string|null $string
 * @property string $updated_at
 * @property string $created_at
 */
class Units extends \yii\db\ActiveRecord
{
    const UNIT_A = "A";
    const UNIT_B = "B";
    const UNIT_C = "C";

    const LIFE_COUNT = 100;

    const UNIT_INFO = [
        self::UNIT_A => [
            "defense" => 30,
            "attack" => 70,
            "life" => self::LIFE_COUNT,
            "recover" => 20,
        ],
        self::UNIT_B => [
            "defense" => 20,
            "attack" => 50,
            "life" => self::LIFE_COUNT,
            "recover" => 15,
        ],
        self::UNIT_C => [
            "defense" => 10,
            "attack" => 40,
            "life" => self::LIFE_COUNT,
            "recover" => 10,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'units';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['string'], 'string'],
            [['updated_at', 'created_at', "win"], 'safe'],
            [['logo', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'logo' => 'Logo',
            'name' => 'Name',
            'string' => 'String',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    public function saveData() {

        $this->logo = UploadedFile::getInstance($this, 'logo');
        $logoName = time().".".$this->logo->extension;
        $this->logo->saveAs('uploads/' . $logoName);
        $this->logo = $logoName;


        $this->save();

    }


}
