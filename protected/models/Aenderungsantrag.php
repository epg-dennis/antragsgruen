<?php

Yii::import('application.models._base.BaseAenderungsantrag');

class Aenderungsantrag extends BaseAenderungsantrag
{
	private $absaetze = null;

    /**
     * @var $clasName string
     * @return GxActiveRecord
     */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return array|int[]
	 */
	public function getAffectedParagraphs() {
		$paras = array();
		foreach ($this->getDiffParagraphs() as $i => $p) if ($p != "") $paras[] = $i;
		return $paras;
	}


	/**
	 * @param array|string[] $paragraphs
	 */
	public function setDiffParagraphs($paragraphs) {
		$this->text_neu = json_encode($paragraphs);
	}

	/**
	 * @return array
	 */
	public function getDiffParagraphs() {
		return json_decode($this->text_neu);
	}


	/**
	 * @return array|AntragAbsatz[]
	 */
	public function getAntragstextParagraphs() {
		if (!is_null($this->absaetze)) return $this->absaetze;
		$this->absaetze = array();
		$komms = $this->aenderungsantragKommentare;

		HtmlBBcodeUtils::initZeilenCounter();
		$arr = HtmlBBcodeUtils::bbcode2html_absaetze(trim($this->aenderung_text));

		for ($i = 0; $i < count($arr["html"]); $i++) {
			$html_plain = HtmlBBcodeUtils::wrapWithTextClass($arr["html_plain"][$i]);
			$this->absaetze[] = new AntragAbsatz($arr["html"][$i], $html_plain, $arr["bbcode"][$i], $this->id, $i, $komms, array());
		}
		return $this->absaetze;
	}

	/**
	 * @return array
	 */
	public function rules() {
	    $rules = parent::rules();
	    $rules_neu = array();
	    foreach ($rules as $rule) if ($rule[1] == "required") {
            $fields = array();
            $x = explode(",", $rule[0]);
            foreach ($x as $y) if (trim($y) != "status_string" && trim($y) != "begruendung_neu") $fields[] = trim($y);
            if (count($fields) > 0) {
                $rule[0] = implode(", ", $fields);
                $rules_neu[] = $rule;
            }
	    } else $rules_neu[] = $rule;

	    return $rules_neu;
	}

	/**
	 * @param int $veranstaltung_id
	 * @param int $limit
	 * @return array|Aenderungsantrag[]
	 */
	public static function holeNeueste($veranstaltung_id = 0, $limit = 5) {
		$oCriteria        = new CDbCriteria();
		$oCriteria->alias = "aenderungsantrag";
		$oCriteria->addNotInCondition("aenderungsantrag.status", IAntrag::$STATI_UNSICHTBAR);
		$oCriteria->with = "antrag";
		if ($veranstaltung_id > 0) $oCriteria->addCondition("antrag.veranstaltung = " . IntVal($veranstaltung_id));
		$oCriteria->addNotInCondition("antrag.status", IAntrag::$STATI_UNSICHTBAR);
		$oCriteria->order           = 'aenderungsantrag.datum_einreichung DESC';
		$dataProvider               = new CActiveDataProvider('Aenderungsantrag', array(
			'criteria'      => $oCriteria,
			'pagination'    => array(
				'pageSize'      => IntVal($limit),
			),
		));
		return $dataProvider->data;
	}

	/**
	 * @return bool
	 */
	public function binInitiatorIn() {

		if (!Yii::app()->user->isGuest) {
			/** @var Person $current_user  */
			$current_user = Person::model()->findByAttributes(array("auth" => Yii::app()->user->id));
			foreach ($this->aenderungsantragUnterstuetzer as $u) {
				/** @var AenderungsantragUnterstuetzer $u */
				if ($u->rolle == AenderungsantragUnterstuetzer::$ROLLE_INITIATOR && $u->unterstuetzer->id == $current_user->id) return true;
			}
		}
		return false;
	}

}