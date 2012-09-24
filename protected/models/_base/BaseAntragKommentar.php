<?php

/**
 * This is the model base class for the table "antrag_kommentar".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "AntragKommentar".
 *
 * Columns in table "antrag_kommentar" available as properties of the model,
 * followed by relations of table "antrag_kommentar" available as properties of the model.
 *
 * @property integer $id
 * @property integer $verfasser_id
 * @property integer $antrag_id
 * @property integer $absatz
 * @property string $text
 * @property string $datum
 * @property integer $status
 *
 * @property Person $verfasser
 * @property Antrag $antrag
 */
abstract class BaseAntragKommentar extends IKommentar {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'antrag_kommentar';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'AntragKommentar|AntragKommentare', $n);
	}

	public static function representingColumn() {
		return 'text';
	}

	public function rules() {
		return array(
			array('text, datum', 'required'),
			array('verfasser_id, antrag_id, absatz, status', 'numerical', 'integerOnly'=>true),
			array('verfasser_id, antrag_id, absatz, status', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, verfasser_id, antrag_id, absatz, text, datum, status', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'verfasser' => array(self::BELONGS_TO, 'Person', 'verfasser_id'),
			'antrag' => array(self::BELONGS_TO, 'Antrag', 'antrag_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'verfasser_id' => null,
			'antrag_id' => null,
			'absatz' => Yii::t('app', 'Absatz'),
			'text' => Yii::t('app', 'Text'),
			'datum' => Yii::t('app', 'Datum'),
			'status' => Yii::t('app', 'Status'),
			'verfasser' => null,
			'antrag' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('verfasser_id', $this->verfasser_id);
		$criteria->compare('antrag_id', $this->antrag_id);
		$criteria->compare('absatz', $this->absatz);
		$criteria->compare('text', $this->text, true);
		$criteria->compare('datum', $this->datum, true);
		$criteria->compare('status', $this->status);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}