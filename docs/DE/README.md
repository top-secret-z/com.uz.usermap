Bilder sagen mehr als 1000 Worte. Das gilt sicher auch für den Wohnort oder den Lieblingsort Ihrer Mitglieder. Statt mühsam im Profil der Mitglieder nachzuschlagen, lassen Sie sich und den Mitgliedern Ihrer Community doch einfach eine Google Maps-Karte mit den Orten der Mitglieder anzeigen. Womit? Natürlich mit  **Benutzer-Karte**. Diese Karte setzt nicht nur ein optisches Highlight in der Community, sondern kann auch neue Möglichkeiten der Kontaktaufnahme zwischen den Mitgliedern schaffen.

### Beschreibung

Diese Anwendung für WoltLab Suite erlaubt es den Mitgliedern Ihrer Community, einen Ort zu definieren, der anderen Benutzern auf einer Karte und im Benutzerprofil angezeigt wird. Hierfür kann entweder ein Benutzerprofilfeld, in das der Ort über die Tastatur eingegeben wird, oder ein grafisches Karten-Interface genutzt werden. Abhängig von der Konfiguration können beide automatisch miteinander synchronisiert werden.

Wenn Sie bereits Benutzerprofilfelder mit einer Ortsangabe nutzen, können Sie diese bei Bedarf im ACP über "Anzeigen aktualisieren - Benutzerkarte aktualisieren" mit der Karte synchronisieren. Die Daten in den vorhandenen Profilfeldern werden dabei zusammengefasst, in das neu installierte Profilfeld "Benutzerkarte" übernommen, dann in Koordinaten umgewandelt (Geocoding) und in die Karte übernommen.

Die Karte ist als Anwendung ausgeführt und bietet vielerlei Möglichkeiten zur Darstellung der Benutzer. So lassen sich die Ortsmarkierungen auf der Karte (Marker) für die Benutzer gruppenspezifisch festlegen; Sie können dafür auch eigene Marker nutzen. Die Karteneinträge lassen sich nach diversen Kriterien, wie Online-Mitglieder oder Follower oder Benutzergruppen, filtern. Sie können in der Karte nach Benutzern und Orten suchen und sich zudem Routen zwischen ausgewählten Benutzern bzw. Orten anzeigen lassen. Diese Routen können nach entsprechendem Aufruf dann auf Google Maps weiterbearbeitet werden.

Ein Klick auf den Benutzer-Marker öffnet ein kleines Fenster, in dem ein Link zum Benutzerprofil und die Ortsangabe des Benutzers dargestellt werden. Das Öffnen des Markers definiert diesen Ort gleichzeitig als Punkt einer ev. Route. Letzteres gilt auch für gesuchte Orte / Benutzer.

Wie in der WoltLab Suite üblich werden auch durch  **Benutzerkarte-**Bedingungen für diverse Zwecke wie Benutzersuche, Hinweise oder Werbung ergänzt, eine Statistik erstellte und zusätzlich Benutzerinformation im Profil und in der Leistenseite angezeigt.

**Benutzerkarte** verfügt über einen sichtbaren Copyright-Hinweis. Dieser kann mit einer kostenpflichtigen Branding-Free-Erweiterung entfernt werden.

### Geocoding

**Benutzerkarte** nutzt Geocoding-Daten von Google Maps und OpenStreetMap. Bei der normalen Nutzung im Frontend wird Google Maps genutzt, bei der Aktualisierung der Benutzerkarte im ACP wird zunächst auf Daten von OpenStreetMap zurückgegriffen. Können dort keine gefunden werden, wird bei Google nach Ortsdaten gesucht. Wesentlicher Grund hierfür sind durch Google festgelegte Einschränkungen des Geocoding-Services. So läßt Google derzeit nur 2.500 kostenlose Anfragen täglich zu; in der Folge kann das Aktualisieren im ACP bei Communities mit vielen Benutzern lange dauern kann. Da aber auch OpenStreetMap Limits für das Geocoding eingeführt hat, kann im ACP immer nur ein Ort pro Sekunde abgearbeitet werden. Bitte haben Sie also beim Aktualisieren ein wenig Geduld.

Erfolgreiche Geocoding-Anfragen bei Google oder OpenStreetMap werden intern in einem Cache zwischengespeichert und stehen für nachfolgende Anfragen zur Verfügung. Die Zeitspanne, nach der der Geocoding-Cache aktualisiert wird, kann im ACP festgelegt werden. Geocoding-relevante Benutzeraktionen werden zusätzlich in einem Protokoll gespeichert, damit bei Problemen nachvollzogen werden, wo es hakt.

Es besteht zudem die Möglichkeit, einen zusätzlichen Browser-API-Schlüssel für das Geocoding anzugeben, der dann intern für einige Funktionen (Suche, Synchronisation) genutzt wird. Dies erleichtert die Umsetzung von Einschränkungen des im System genutzten primären Schlüssels. Der zusätzliche Schlüssel sollte mit geeigneten Limits auf die Geocoding-API eingeschränkt werden.

### Konfiguration

Neben Benutzergruppenrechten zur Nutzung der Karte lassen sich im ACP diverse Anzeige-Optionen für Karte konfigurieren und vor allem Einstellungen zur Datensynchronisation vornehmen.

Ab Version 5.2.1 ist das Ausblenden von deaktivierten, gesperrten und/oder inaktiven Benutzern über Optionen möglich.

### Hinweise

Bei  **Benutzerkarte** handelt es sich um eine Endanwendung. WoltLab Suite Core wird jedoch nicht mitgeliefert. Darüber hinaus müssen Anpassungen (rewrite / .htaccess bzw. nginx-Äquivalent) vorgenommen werden, wenn Link-Umschreibungen (SEO) aktiviert sind.

Beim Öffnen der Benutzerkarte werden zunächst immer alle Benutzereinträge geladen. Bei Communities mit vielen Benutzern kostet das naturgemäß Zeit (das Laden von 10.000 Benutzereinträgen dauert auf meinem Root-Server bis zu 10 Sekunden) und Speicherplatz für die Einträge. Die für WoltLab Suite angegebenen Mindestvoraussetzungen (memory_limit ab 128 MB Speicher) reichen bei großen Communities daher unter Umständen nicht aus.

Zur Nutzung der Benutzerkarte muss im ACP zwingend ein Browser-API-Schlüssel von Google vorhanden und muss die Community für Google Maps freigeschaltet sein. Siehe  [Get a Key/Authentication](https://developers.google.com/maps/documentation/javascript/get-api-key).  
Google begrenzt zudem nicht nur die Geocoding-Anfragen sondern auch die Kartenaufrufe (zurzeit 25.000). Mehr Informationen dazu finden sich unter  [API Nutzungsbeschränkungen](https://developers.google.com/maps/documentation/javascript/usage).