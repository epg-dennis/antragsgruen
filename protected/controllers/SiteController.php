<?php

class SiteController extends Controller
{

	public $multimenu = null;
	public $menus_html = null;
	public $breadcrumbs_topname = null;

	/**
	 *
	 */
	public function actionIndex()
	{
		$veranstaltung_id = (isset($_REQUEST["id"]) ? IntVal($_REQUEST["id"]) : Yii::app()->params['standardVeranstaltung']);
		$this->actionVeranstaltung($veranstaltung_id);
	}

	/**
	 *
	 */
	public function actionImpressum()
	{
		/** @var Texte $v */
		$v = Texte::model()->findByAttributes(array("text_id" => "impressum"));

		$this->render('content', array(
			"title"            => "Impressum",
			"breadcrumb_title" => "Impressum",
			"content"          => $v->text,
			"editlink"         => (Yii::app()->user->getState("role") == "admin" ? "/admin/texte/update/id/" . $v->id . "/" : null),
		));
	}

	/**
	 *
	 */
	public function actionHilfe()
	{
		/** @var Texte $v */
		$v = Texte::model()->findByAttributes(array("text_id" => "hilfe"));

		$this->render('content', array(
			"title"            => "Hilfe",
			"breadcrumb_title" => "Hilfe",
			"content"          => $v->text,
			"editlink"         => (Yii::app()->user->getState("role") == "admin" ? "/admin/texte/update/id/" . $v->id . "/" : null),
		));
	}

	/**
	 * @param Veranstaltung $veranstaltung
	 * @return array
	 */
	private function getFeedAntraegeData(&$veranstaltung)
	{
		$veranstaltung_id = IntVal($veranstaltung->id);
		if ($veranstaltung_id == 0 && isset($_REQUEST["id"])) $veranstaltung_id = IntVal($_REQUEST["id"]);

		$antraege = Antrag::holeNeueste($veranstaltung_id, 20);

		$data = array();
		foreach ($antraege as $ant) $data[AntraegeUtils::date_iso2timestamp($ant->datum_einreichung) . "_antrag_" . $ant->id] = array(
			"title"       => "Neuer Antrag: " . $ant->revision_name . " - " . $ant->name,
			"link"        => Yii::app()->getBaseUrl(true) . "/antrag/anzeige/?id=" . $ant->id,
			"dateCreated" => AntraegeUtils::date_iso2timestamp($ant->datum_einreichung),
			"content"     => "<h2>Antrag</h2>" . HtmlBBcodeUtils::bbcode2html($ant->text) . "<br>\n<br>\n<br>\n<h2>Begründung</h2>" . HtmlBBcodeUtils::bbcode2html($ant->begruendung),
		);
		return $data;
	}

	/**
	 * @param Veranstaltung $veranstaltung
	 * @return array
	 */
	private function getFeedAenderungsantraegeData(&$veranstaltung)
	{
		$veranstaltung_id = IntVal($veranstaltung->id);
		if ($veranstaltung_id == 0 && isset($_REQUEST["id"])) $veranstaltung_id = IntVal($_REQUEST["id"]);

		$antraege = Aenderungsantrag::holeNeueste($veranstaltung_id, 20);

		$data = array();
		foreach ($antraege as $ant) $data[AntraegeUtils::date_iso2timestamp($ant->datum_einreichung) . "_aenderungsantrag_" . $ant->id] = array(
			"title"       => "Neuer Änderungsantrag: " . $ant->revision_name . " zu " . $ant->antrag->revision_name . " - " . $ant->antrag->name,
			"link"        => Yii::app()->getBaseUrl(true) . "/aenderungsantrag/anzeige/?id=" . $ant->id,
			"dateCreated" => AntraegeUtils::date_iso2timestamp($ant->datum_einreichung),
			"content"     => "<h2>Antrag</h2>" . HtmlBBcodeUtils::bbcode2html($ant->aenderung_text) . "<br>\n<br>\n<br>\n<h2>Begründung</h2>" . HtmlBBcodeUtils::bbcode2html($ant->aenderung_begruendung),
		);
		return $data;
	}

	/**
	 * @param Veranstaltung $veranstaltung
	 * @return array
	 */
	private function getFeedAntragKommentarData(&$veranstaltung)
	{
		$veranstaltung_id = IntVal($veranstaltung->id);
		if ($veranstaltung_id == 0 && isset($_REQUEST["id"])) $veranstaltung_id = IntVal($_REQUEST["id"]);

		$antraege = AntragKommentar::holeNeueste($veranstaltung_id, 20);

		$data = array();
		foreach ($antraege as $ant) $data[AntraegeUtils::date_iso2timestamp($ant->datum) . "_kommentar_" . $ant->id] = array(
			"title"       => "Neuer Kommentar zu: " . $ant->antrag->revision_name . " - " . $ant->antrag->name,
			"link"        => Yii::app()->getBaseUrl(true) . "/antrag/anzeige/?id=" . $ant->antrag->id . "&kommentar=" . $ant->id . "#komm" . $ant->id,
			"dateCreated" => AntraegeUtils::date_iso2timestamp($ant->datum),
			"content"     => HtmlBBcodeUtils::bbcode2html($ant->text),
		);
		return $data;
	}

	/**
	 * @param int $veranstaltung_id
	 */
	public function actionFeedAntraege($veranstaltung_id = 0)
	{
		if ($veranstaltung_id == 0 && isset($_REQUEST["id"])) $veranstaltung_id = IntVal($_REQUEST["id"]);
		/** @var Veranstaltung $veranstaltung */
		$veranstaltung = Veranstaltung::model()->findByPk($veranstaltung_id);
		$sprache = $veranstaltung->getSprache();
		$this->renderPartial('feed', array(
			"veranstaltung_id" => $veranstaltung_id,
			"feed_title"       => $sprache->get("Anträge"),
			"feed_description" => str_replace("%veranstaltung%", $veranstaltung->name, $sprache->get("feed_desc_antraege")),
			"data"             => $this->getFeedAntraegeData($veranstaltung),
			"sprache"          => $sprache,
		));
	}

	/**
	 * @param int $veranstaltung_id
	 */
	public function actionFeedAenderungsantraege($veranstaltung_id = 0)
	{
		if ($veranstaltung_id == 0 && isset($_REQUEST["id"])) $veranstaltung_id = IntVal($_REQUEST["id"]);
		/** @var Veranstaltung $veranstaltung */
		$veranstaltung = Veranstaltung::model()->findByPk($veranstaltung_id);
		$sprache = $veranstaltung->getSprache();
		$this->renderPartial('feed', array(
			"veranstaltung_id" => $veranstaltung_id,
			"feed_title"       => $sprache->get("Änderungsanträge"),
			"feed_description" => str_replace("%veranstaltung%", $veranstaltung->name, $sprache->get("feed_desc_aenderungsantraege")),
			"data"             => $this->getFeedAenderungsantraegeData($veranstaltung),
			"sprache"          => $sprache,
		));
	}

	/**
	 * @param int $veranstaltung_id
	 */
	public function actionFeedKommentare($veranstaltung_id = 0)
	{
		if ($veranstaltung_id == 0 && isset($_REQUEST["id"])) $veranstaltung_id = IntVal($_REQUEST["id"]);
		/** @var Veranstaltung $veranstaltung */
		$veranstaltung = Veranstaltung::model()->findByPk($veranstaltung_id);
		$sprache = $veranstaltung->getSprache();
		$this->renderPartial('feed', array(
			"veranstaltung_id" => $veranstaltung_id,
			"feed_title"       => $sprache->get("Kommentare"),
			"feed_description" => str_replace("%veranstaltung%", $veranstaltung->name, $sprache->get("feed_desc_kommentare")),
			"data"             => $this->getFeedAntragKommentarData($veranstaltung),
			"sprache"          => $veranstaltung->getSprache(),
		));
	}


	/**
	 * @param int $veranstaltung_id
	 */
	public function actionFeedAlles($veranstaltung_id = 0)
	{
		if ($veranstaltung_id == 0 && isset($_REQUEST["id"])) $veranstaltung_id = IntVal($_REQUEST["id"]);
		/** @var Veranstaltung $veranstaltung */
		$veranstaltung = Veranstaltung::model()->findByPk($veranstaltung_id);

		$data1 = $this->getFeedAntraegeData($veranstaltung);
		$data2 = $this->getFeedAenderungsantraegeData($veranstaltung);
		$data3 = $this->getFeedAntragKommentarData($veranstaltung);

		$data = array_merge($data1, $data2, $data3);
		krsort($data);

		$this->renderPartial('feed', array(
			"veranstaltung_id" => $veranstaltung_id,
			"feed_title"       => "Anträge, Änderungsanträge und Kommentare",
			"data"             => $data,
			"sprache"          => $veranstaltung->getSprache(),
		));

	}


	/**
	 * @param int $veranstaltung_id
	 */
	public function actionVeranstaltung($veranstaltung_id = 0)
	{
		$veranstaltung_id = IntVal($veranstaltung_id);
		if ($veranstaltung_id == 0 && isset($_REQUEST["id"])) $veranstaltung_id = IntVal($_REQUEST["id"]);

		$this->layout = '//layouts/column2';


		/** @var $veranstaltung Veranstaltung */
		$veranstaltung = Veranstaltung::model()->
			with(array(
			'antraege'                    => array(
				'joinType' => "LEFT OUTER JOIN",
				'on'       => "`antraege`.`veranstaltung` = `t`.`id` AND `antraege`.`status` NOT IN (" . implode(", ", IAntrag::$STATI_UNSICHTBAR) . ")",
			),
			'antraege.aenderungsantraege' => array(
				'joinType' => "LEFT OUTER JOIN",
				"on"       => "`aenderungsantraege`.`antrag_id` = `antraege`.`id` AND `aenderungsantraege`.`status` NOT IN (" . implode(", ", IAntrag::$STATI_UNSICHTBAR) . ")",
			),
		))->findByPk($veranstaltung_id);

		/** @var $antraege array|Antrag[] */
		$antraege        = $veranstaltung->antraege;
		$antraege_sorted = array();
		$warnung         = false;
		foreach ($antraege as $ant) {
			if (!isset($antraege_sorted[Antrag::$TYPEN[$ant->typ]])) $antraege_sorted[Antrag::$TYPEN[$ant->typ]] = array();
			$key = $ant->revision_name;
			if (isset($antraege_sorted[Antrag::$TYPEN[$ant->typ]][$key]) && !$warnung) {
				$warnung = true;
				Yii::app()->user->setFlash("error", "Es können nicht alle Anträge angezeigt werden, da mindestens ein Kürzel ($key) mehrfach vergeben ist.");
			}
			$antraege_sorted[Antrag::$TYPEN[$ant->typ]][$key] = $ant;
		}

		/** @var null|Person $ich */
		if (Yii::app()->user->isGuest) $ich = null;
		else {
			$ich = Person::model()->findByAttributes(array("auth" => Yii::app()->user->id));
		}

		$neueste_aenderungsantraege = Aenderungsantrag::holeNeueste($veranstaltung_id, 5);
		$neueste_antraege           = Antrag::holeNeueste($veranstaltung_id, 5);
		$neueste_kommentare         = AntragKommentar::holeNeueste($veranstaltung_id, 3);

		$meine_antraege           = array();
		$meine_aenderungsantraege = array();

		if ($ich) {
			$oCriteria        = new CDbCriteria();
			$oCriteria->alias = "antrag_unterstuetzer";
			$oCriteria->join  = "JOIN `antrag` ON `antrag`.`id` = `antrag_unterstuetzer`.`antrag_id`";
			$oCriteria->addCondition("`antrag`.`veranstaltung` = " . IntVal($veranstaltung_id));
			$oCriteria->addCondition("`antrag_unterstuetzer`.`unterstuetzer_id` = " . IntVal($ich->id));
			$oCriteria->addCondition("`antrag`.`status` != " . IAntrag::$STATUS_GELOESCHT);
			$oCriteria->order = '`datum_einreichung` DESC';
			$dataProvider     = new CActiveDataProvider('AntragUnterstuetzer', array(
				'criteria' => $oCriteria,
			));
			$meine_antraege   = $dataProvider->data;

			$oCriteria        = new CDbCriteria();
			$oCriteria->alias = "aenderungsantrag_unterstuetzer";
			$oCriteria->join  = "JOIN `aenderungsantrag` ON `aenderungsantrag`.`id` = `aenderungsantrag_unterstuetzer`.`aenderungsantrag_id`";
			$oCriteria->join .= " JOIN `antrag` ON `aenderungsantrag`.`antrag_id` = `antrag`.`id`";
			$oCriteria->addCondition("`antrag`.`veranstaltung` = " . IntVal($veranstaltung_id));
			$oCriteria->addCondition("`aenderungsantrag_unterstuetzer`.`unterstuetzer_id` = " . IntVal($ich->id));
			$oCriteria->addCondition("`antrag`.`status` != " . IAntrag::$STATUS_GELOESCHT);
			$oCriteria->addCondition("`aenderungsantrag`.`status` != " . IAntrag::$STATUS_GELOESCHT);
			$oCriteria->order         = '`aenderungsantrag`.`datum_einreichung` DESC';
			$dataProvider             = new CActiveDataProvider('AenderungsantragUnterstuetzer', array(
				'criteria' => $oCriteria,
			));
			$meine_aenderungsantraege = $dataProvider->data;
		}

		/** @var Texte $texto */
		$texto           = Texte::model()->findByAttributes(array("veranstaltung_id" => $veranstaltung->id, "text_id" => "startseite"));
		$einleitungstext = ($texto ? $texto->text : null);

		$this->render('veranstaltung_index', array(
			"veranstaltung"              => $veranstaltung,
			"einleitungstext"            => $einleitungstext,
			"antraege"                   => $antraege_sorted,
			"ich"                        => $ich,
			"neueste_antraege"           => $neueste_antraege,
			"neueste_kommentare"         => $neueste_kommentare,
			"neueste_aenderungsantraege" => $neueste_aenderungsantraege,
			"meine_antraege"             => $meine_antraege,
			"meine_aenderungsantraege"   => $meine_aenderungsantraege,
			"sprache"                    => $veranstaltung->getSprache(),
		));
	}


	/**
	 *
	 */
	public function actionMeineAntraege()
	{
		$this->render('meineAntraege');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if ($error = Yii::app()->errorHandler->error) {
			if (Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 *
	 */
	public function actionLogin()
	{
		$model = new OAuthLoginForm();
		if (isset($_REQUEST["OAuthLoginForm"])) $model->attributes = $_REQUEST["OAuthLoginForm"];

		/** @var LightOpenID $loid */
		$loid = Yii::app()->loid->load();
		if (isset($_REQUEST["openid_mode"])) {
			if ($_REQUEST['openid_mode'] == 'cancel') {
				$err = Yii::t('core', 'Authorization cancelled');
			} else {
				try {
					$us = new AntragUserIdentity($loid);
					if ($us->authenticate()) {
						Yii::app()->user->login($us);
						$user = Person::model()->findByAttributes(array("auth" => $us->getId()));
						if (!$user) {
							$user                 = new Person;
							$user->auth           = $us->getId();
							$user->name           = $us->getName();
							$user->email          = $us->getEmail();
							$user->angelegt_datum = date("Y-m-d H:i:s");
							$user->status         = Person::$STATUS_CONFIRMED;
							$user->typ            = Person::$TYP_PERSON;
							$user->admin          = 0;
							$user->save();
						} else {
							if ($user->admin) {
								//$openid->setState("role", "admin");
								Yii::app()->user->setState("role", "admin");
							}
						}
						Yii::app()->user->setState("person_id", $user->id);
						Yii::app()->user->setFlash('success', 'Willkommen!');
						$this->redirect(Yii::app()->homeUrl);
					} else {
						Yii::app()->user->setFlash("error", "Leider ist beim Einloggen ein Fehler aufgetreten.");
						$this->render('login', array());
					}
				} catch (Exception $e) {
					$err = Yii::t('core', $e->getMessage());
					Yii::app()->user->setFlash("error", "Leider ist beim Einloggen ein Fehler aufgetreten:<br>" . $e->getMessage());
					$this->render('login', array());
				}
			}

			if (!empty($err)) Yii::app()->user->setFlash("error", $err);
		} elseif (isset($_REQUEST["OAuthLoginForm"])) {
			if ($model->wurzelwerk != "") $loid->identity = "https://" . $model->wurzelwerk . ".netzbegruener.in/";
			else $loid->identity = $model->openid_identifier;

			$loid->required  = array('namePerson/friendly', 'contact/email'); //Try to get info from openid provider
			$loid->realm     = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
			$loid->returnUrl = $loid->realm . $_SERVER['REQUEST_URI']; //getting return URL
			if (empty($err)) {
				try {
					$url = $loid->authUrl();
					$this->redirect($url);
				} catch (Exception $e) {
					$err = Yii::t('core', $e->getMessage());
				}
			}
			if (!empty($err)) Yii::app()->user->setFlash("error", $err);
		}

		$this->render('login', array("model" => $model));
	}


	/**
	 *
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		Yii::app()->user->setFlash("success", "Bis bald!");
		$this->redirect(Yii::app()->homeUrl);
	}
}
