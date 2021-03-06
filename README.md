Antragsgrün
===========

Antragsgrün ist ein Antragsverwaltungssystem, das mit der [Netzbegrünung](http://blog.netzbegruenung.de/) entwickelt wird.

Es ist auf zwei Szenarien zugeschnitten:
- Das Kommentieren von Dokumenten - insbesondere des Wahlprogramms
- Die Vorbereitung von Parteitagen - insbesondere das Einreichen und Versionieren von Anträgen, das Kommentieren von Anträgen und das Einreichen von Änderungsanträgen.

Es ist NICHT:
- Ein Abstimmungstool. Eine eventuelle Beschlussfassung über Anträge findet auf andere Weise statt (Parteitag, Programmkommission, etc.)
- Ein "Vor-Ort"-Tool. Dafür empfehle ich [OpenSlides](http://openslides.org/de/) . Ich strebe mittelfristig an, den Export von Anträgen von Antragsgrün zu Openslides soweit möglich zu erleichtern.


Login und Berechtigungen
------------------------
Wichtig ist uns eine möglichst niederschwellige Benutzung. Ein Login ist zum Schreiben von Kommentaren, Änderungsanträgen und Anträgen nicht unbedingt nötig. Ggf. müssen (Änderungs-)Anträge aber von einem Admin freigeschaltet werden, um Spam zu verhindern.

Ein Login ist über OpenID möglich. Besonders hervorgehoben ist dabei die Möglichkeit, sich mit den Wurzelwerk-Logindaten (per OpenID) anzumelden.

Ob ein Login nötig ist, und ob Anträge / Änderungsanträge erst freigeschaltet werden müssen, ist pro Veranstaltung / Wahlprogramm separat einstellbar.

Differenziertere Rechtestrukturen, wie z.B. dass für die Antragsstellung für einen Parteitag auch XY UnterstützerInnen angegeben werden müssen, sind mit wenig (aber nicht ganz ohne) Programmierarbeit einzurichten.

Textfluss / Absätze
-------------------
Wichtig ist uns, das von klassischen "Offline-Anträgen" her bekannte System von definierten Zeilennummern beizubehalten. Eine Zeile hat maximal 80 Zeichen, bei eingerückten Listen bzw. Zitaten maximal 60 Zeichen.

Kommentare und Änderungsanträge beziehen sich jeweils auf ganze Absätze. Ein neuer Absatz beginnt nach einer Leerzeile.

Als Textformatierungen sind Fett/Kursivschrift möglich, Unter- und Durchstreichungen, Links, (un)nummerierte Listen und eingerückte Kommentare.

Versionierung / Diffs
---------------------
Bei jedem Änderungsantrag ist muss der/die AntragsstellerIn auch angeben, wie der Antrag bzw. die zu ändernden Absätze danach aussehen soll. Ein Änderungsantrag kann auch mehrere Absätze ändern, neue einfügen bzw. löschen.

Beim betreffenden Antrag wird an den betreffenden Absätzen markiert, dass hierzu Änderungsanträge (und ggf. Kommentare) vorliegen. Die Änderungen können auch in "Diff-Ansicht" angezeigt werden, das heißt, dass neu eingefügte Textpassagen grün erscheinen, gelöschte rot durchgestrichen.

[TODO] Wenn der/die AntragsstellerIn sich entschließt, eine neue Version des Antrags zu erstellen (A1neu), ist eine Änderungsansicht zum vorigen Antrag verfügbar.

RSS-Feeds
---------
Es gibt je einen RSS-Feed über neue Anträge zu einer Veranstaltung, neue Änderungsanträge bzgl. einer Veranstaltung und neue Kommentare zu einem Antrag einer Veranstaltung. Außerdem einen Sammel-Feed, in denen alle Einträge der drei vorigen Feeds auftauchen.

[TODO] Es wird einen Feed pro Antrag geben, in dem neue Änderungsanträge und Kommentare zu dem betreffenden Antrag gelistet werden.

[TODO] Es ist die Anbindung an einen Twitter-Account pro Veranstaltung geplant, über den neue (Änderung-)Anträge automatisch gepostet werden können.

Demo
----
Im Testbetrieb wird es derzeit hier eingestzt: http://antraege.hoessl.eu/




Kontakt:
--------
- tobias@hoessl.eu
- [@TobiasHoessl](https://twitter.com/TobiasHoessl)
- http://www.hoessl.eu/