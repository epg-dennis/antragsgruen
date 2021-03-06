<?php

require_once 'Horde/String.php';
require_once 'Horde/Text/Diff.php';
require_once 'Horde/Text/Diff/Renderer.php';
require_once 'Horde/Text/Diff/Renderer/Unified.php';
require_once 'Horde/Text/Diff/Renderer/Inline.php';
require_once 'Horde/Text/Diff/Engine/Native.php';
require_once 'Horde/Text/Diff/Engine/Xdiff.php';
require_once 'Horde/Text/Diff/Op/Base.php';
require_once 'Horde/Text/Diff/Op/Add.php';
require_once 'Horde/Text/Diff/Op/Copy.php';
require_once 'Horde/Text/Diff/Op/Change.php';
require_once 'Horde/Text/Diff/Op/Delete.php';

class Horde_Text_Diff_Renderer_Inline_Antrag extends Horde_Text_Diff_Renderer_Inline
{
	protected $_leading_context_lines = 1;
	protected $_trailing_context_lines = 1;
}

class Horde_Text_Diff_Renderer_Inline_Antrag1000 extends Horde_Text_Diff_Renderer_Inline
{
	protected $_leading_context_lines = 1000;
	protected $_trailing_context_lines = 1000;
}

/**
 *
 */
class DiffUtils
{

	/**
	 * @static
	 * @param Horde_Text_Diff $diff
	 * @return string
	 */
	public static function diff2text($diff)
	{
		$diff_text2 = "";
		$edits      = $diff->getDiff();
		$line       = 0;

		foreach ($edits as $edit) {
			if (get_class($edit) == "Horde_Text_Diff_Op_Add") {
				$final = implode("\n", $edit->final);
				if (trim($final, " \t\n\r") != "") {
					if (mb_strpos($final, "#ZEILE#") === 0) {
						$diff_text2 .= "Nach Zeile " . $line . " einfügen: [QUOTE]" . $final . "[/QUOTE]\n\n";
					} else {
						$diff_text2 .= "Folgenden Absatz einfügen: [QUOTE]" . $final . "[/QUOTE]\n\n";
					}
				}
			}
			if (get_class($edit) == "Horde_Text_Diff_Op_Delete") {
				$orig = implode("\n", $edit->orig);
				if (trim($orig, " \t\n\r") != "") {
					$zeilen = substr_count($orig, "#ZEILE#");
					if (mb_strpos($orig, "#ZEILE#") === 0) {
						$diff_text2 .= "Streiche Zeile " . ($line + 1);
						if ($zeilen > 1) $diff_text2 .= " bis " . ($line + $zeilen);
						$diff_text2 .= ": [QUOTE]" . $orig . "[/QUOTE]\n\n";
					}
				} else {
					$diff_text2 .= "Folgenden Absatz löschen: [QUOTE]" . $orig . "[/QUOTE]\n\n";
				}
			}
			if (get_class($edit) == "Horde_Text_Diff_Op_Change") {
				$orig  = implode("\n", $edit->orig);
				$final = implode("\n", $edit->final);

				if (trim($orig, " \t\n\r") != "" || trim($final, " \t\n\r") != "") {
					$inab = (substr_count($orig, "#ZEILE#") > 1 ? "ab" : "in");
					$diff_text2 .= "Ersetze $inab Zeile " . ($line + 1) . ":\n[QUOTE]" . $orig . "[/QUOTE]durch:[QUOTE]" . $final . "[/QUOTE]\n\n";
				}
			}

			if (is_array($edit->orig)) {
				$line += substr_count(implode("\n", $edit->orig), "#ZEILE#");
			}
		}

		$diff_text2 = str_replace("\n#ZEILE#", "", $diff_text2);
		$diff_text2 = str_replace("#ZEILE#", "", $diff_text2);
		$diff_text2 = str_replace("#ABSATZ#", "", $diff_text2);

		return $diff_text2;
	}

	public static function getTextDiffMitZeilennummern($string1 = "", $string2 = "")
	{
		HtmlBBcodeUtils::initZeilenCounter();
		$arr1  = HtmlBBcodeUtils::bbcode2zeilen_absaetze(trim($string1));
		$text1 = implode("\n#ABSATZ#\n", $arr1);

		HtmlBBcodeUtils::initZeilenCounter();
		$arr2  = HtmlBBcodeUtils::bbcode2zeilen_absaetze(trim($string2));
		$text2 = implode("\n#ABSATZ#\n", $arr2);

		$diff = new Horde_Text_Diff('native', array(explode("\n", $text1), explode("\n", $text2)));
		return $diff;
	}


	/**
	 * @static
	 * @param string $string1
	 * @param string $string2
	 * @return Horde_Text_Diff
	 */
	public static function getTextDiff($string1 = "", $string2 = "")
	{
		$diff = new Horde_Text_Diff('native', array(explode("\n", $string1), explode("\n", $string2)));
		return $diff;
	}

	/**
	 * @static
	 * @param Horde_Text_Diff $diff
	 * @param bool $empty_comment
	 * @return string
	 */
	public static function renderDiff($diff, $empty_comment = false)
	{
		$renderer  = new Horde_Text_Diff_Renderer_Inline_Antrag();
		$diff_text = $renderer->render($diff);
		if ($diff_text == "" && $empty_comment) $diff_text = "<em>keine Änderung</em>";
		return $diff_text;
	}

	/**
	 * @static
	 * @param Horde_Text_Diff $diff
	 * @return string
	 */
	public static function renderAbsatzDiff($diff)
	{
		$renderer  = new Horde_Text_Diff_Renderer_Inline_Antrag1000();
		$diff_text = $renderer->render($diff);
		return $diff_text;
	}


	/**
	 * @static
	 * @param string $text
	 * @return string
	 */
	private static function bbNormalizeForDiff($text)
	{
		$text = str_replace("\r", "", $text);
		$text = str_replace(chr(13), "", $text);
		$text = preg_replace("/ {2,}/siu", " ", $text);
		$text = trim($text);
		$text = preg_replace_callback("/(\[\/?(?:b|i|u|s|list|ulist|quote))([^a-z])/siu", function($matches) { return mb_strtoupper($matches[1]) . $matches[2]; }, $text);
		$text = preg_replace("/(\[list[^\]]*\])\\n*\[/siu", "\\1\n[", $text);
		$text = preg_replace("/\n*\[\*/siu", "\n[*", $text);
		$text = str_replace("\r", "", $text);
		$text = str_replace(chr(13), "", $text);

		return $text;
	}

	/**
	 * @static
	 * @param string $text
	 * @return string
	 */
	private static function htmlNormalizeForDiff($text)
	{
		$text = str_replace("\r", "", $text);
		$text = str_replace(chr(13), "", $text);
		$text = preg_replace("/<\/li>[ \\n]*<\/ol>/siu", "</li>\n</ol>", $text);
		$text = preg_replace("/<\/li>[ \\n]*<\/ul>/siu", "</li>\n</ul>", $text);
		return $text;
	}

	/**
	 * @static
	 * @param string $text_alt
	 * @param string $text_neu
	 * @return string
	 */
	public static function renderBBCodeDiff2HTML($text_alt, $text_neu)
	{
		$text_alt = static::bbNormalizeForDiff($text_alt);
		$text_neu = static::bbNormalizeForDiff($text_neu);

		$diff = DiffUtils::getTextDiff($text_alt, $text_neu);
		$absatz = DiffUtils::renderAbsatzDiff($diff);

		$diffstr = HtmlBBcodeUtils::bbcode2html($absatz);

		$diffstr = str_ireplace(
			array("&lt;ins&gt;", "&lt;/ins&gt;", "&lt;del&gt;", "&lt;/del&gt;"),
			array("<ins>", "</ins>", "<del>", "</del>"),
			$diffstr);

		if ($diffstr == "") $diffstr = HtmlBBcodeUtils::bbcode2html($text_alt);

		$diffstr = HtmlBBcodeUtils::wrapWithTextClass($diffstr);
		return $diffstr;
	}

}