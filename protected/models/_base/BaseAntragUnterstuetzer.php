<?php

/**
 * This is the model base class for the table "antrag_unterstuetzer".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "AntragUnterstuetzer".
 *
 * Columns in table "antrag_unterstuetzer" available as properties of the model,
 * followed by relations of table "antrag_unterstuetzer" available as properties of the model.
 *
 * @property integer $antrag_id
 * @property integer $unterstuetzer_id
 * @property string $rolle
 * @property string $kommentar
 *
 * @property Antrag $antrag
 * @property Person $unterstuetzer
 */
abstract class BaseAntragUnterstuetzer extends IUnterstuetzer {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'antrag_unterstuetzer';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'AntragUnterstuetzer|AntragUnterstuetzer', $n);
	}

	public static function representingColumn() {
		return 'rolle';
	}

	public function rules() {
		return array(
			array('antrag_id, unterstuetzer_id, rolle', 'required'),
			array('antrag_id, unterstuetzer_id', 'numerical', 'integerOnly'=>true),
			array('rolle', 'length', 'max'=>12),
			array('kommentar', 'safe'),
			array('kommentar', 'default', 'setOnEmpty' => true, 'value' => null),
			array('antrag_id, unterstuetzer_id, rolle, kommentar', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'antrag' => array(self::BELONGS_TO, 'Antrag', 'antrag_id'),
			'unterstuetzer' => array(self::BELONGS_TO, 'Person', 'unterstuetzer_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'antrag_id' => null,
			'unterstuetzer_id' => null,
			'rolle' => Yii::t('app', 'Rolle'),
			'kommentar' => Yii::t('app', 'Kommentar'),
			'antrag' => null,
			'unterstuetzer' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('antrag_id', $this->antrag_id);
		$criteria->compare('unterstuetzer_id', $this->unterstuetzer_id);
		$criteria->compare('rolle', $this->rolle, true);
		$criteria->compare('kommentar', $this->kommentar, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}