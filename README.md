turistautak osm track import
============================

A [turistautak.hu](http://turistautak.hu/) 2015. február 1-től ODbL licenc alatt szabadon elérhetővé tette térképi állománya mellett a feltöltött [nyomvonalakat](http://turistautak.hu/tracks.php) is. Ezek egy része a személyi átfedések révén már az [OSM nyomvonal-adatbázisban](http://www.openstreetmap.org/traces) is megvan, de jelentős részük még nincs. Az a cél, hogy a turistautak.hu összes nyomvonala kerüljön fel OSM-re, hogy térképszerkesztéskor letölthető legyen.

Legjobb lenne egységes algoritmussal lefuttatni az áttöltést, az ütközések elkerülése érdekében egy szálon. Ha esetleg több példányban futtatjuk, akkor azonosító-tartományonkénti bontásban. A munkához processzor és sávszélesség mindenképpen kell, valamint tárhely is, ha archiválni is szeretnénk a műveletet.

Javaslom, hogy írjunk rá programot, lehetőleg platformfüggetlenre, de mindenképpen linuxon futtathatóra. Én PHP-ben írnám, szépen működik parancssorban.

Fontos, hogy valahogyan kihagyjuk azokat, amelyeket már áttöltöttünk. Ennek meghatározása a legnehezebb kérdés. Nem lehetetlen automatizálni, hiszen az api elérhető és megvizsgálható, hogy egy adott nyomvonal pontjai megjelennek-e a letöltésben, de ez iszonyatosan sok műveletet igényel, mivel egy nyomvonal nagyon sok pontból állhat, előfordulhat hogy osm-re megszűrve került fel (én például így töltöm), stb. Ezen felül nehezíti az azonosítást, ha az osm-re időbélyeg nélkül került, vagy a felhasznált átalakítás során elmászott az időzóna.

Lépések:

1. Letöltjük az összes nyomvonalat az osm szerverről és betoljuk adatbázisba. Ez azért kell, hogy meghatározhassuk, fent van-e már a feltöltendő. Ezt elég egyszer megtenni és azért nem turistautak.hu feltöltésenként, mert abból 40 ezer van. Egyúttal feltételezzük, hogy a turistautak.hu állományai között nincs ismétlődés, bár ez is ellenőrizhető, lásd alább. Nem mindegy továbbá, hogy mely területről töltünk le nyomvonalakat, hiszen a turistautak.hu-nak csak a térképe áll meg a határon, a nyomvonalak nem. A le nem töltött területen nem tudunk duplázódást vizsgálni. [[#1]](https://github.com/kolesar-andras/turistautak-osm-track-import/issues/1)

2. Letöltjük a fájlokat a turistautak.hu-ról. Ez sávszélesség kérdése, 12 GB a szerveren. Érdemes egyszerre túl lenni rajta, mert ha egyesével állunk neki és az ötszázadiknál belefutunk egy hibába, amelynél kiderül hogy egy csomó előzőt is érint, akkor bonyolultabb újrakezdeni. [[#2]](https://github.com/kolesar-andras/turistautak-osm-track-import/issues/2)

3. Megszűrjük a fájlokat. Eleve kiesnek a képek és azok, amelyek nem nyomvonalat tartalmaznak. Kérdés viszont, hogy mit tegyünk a zip fájlokkal, azokat hová és milyen néven csomagoljuk ki, mit kezdünk a névben szereplő problémás karakterekkel, stb. [[#3]](https://github.com/kolesar-andras/turistautak-osm-track-import/issues/3)

4. Átalakítjuk gpsbabellel a különböző formátumokat egységesen gpx.gz-re. Ez önmagában napokig fut, ráadásul az elején sokszor újra kell kezdeni, mert akkor jönnek elő a legkülönbözőbb hibák. Tavaly 3-4 felhasználó nyomvonalaival kipróbáltam, bámulatosan sok gond jött elő. [[#4]](https://github.com/kolesar-andras/turistautak-osm-track-import/issues/4)

5. Megpróbáljuk kitalálni, hogy nincs-e már fent a nyomvonal. Ehhez okos programot kell írnunk, ami vesz néhány reprezentatív mintát az adott csomagból, megnézi hogy megvan-e az adatbázisban, szükség esetén tovább vizsgálódik és a végén ad valami valószínűségi értéket. Ha ez magas, akkor inkább hagyja ki és írjon listát róla. [[#5]](https://github.com/kolesar-andras/turistautak-osm-track-import/issues/5)

6. Megszűrjük a nyomvonalat. Csomó őrült van, aki másodperces nyomvonalat tölt fel gyaloglásról. Nem lenne arcom ilyet osm-re tölteni. Az említett próbák során felére-harmadára esett vissza az állományok összmérete a szűréstől, aprólékosan ellenőriztem hogy szemmel gyakorlatilag észrevehetetlen a különbség. [[#6]](https://github.com/kolesar-andras/turistautak-osm-track-import/issues/6)

7. Ha akarjuk, betölthetjük a nyomvonal-adatbázisba a feldolgozott gpx-et, persze megkülönböztetve az osm adattól, a végén pedig odaajándékozhatjuk az egyesületnek, amelyből ők osm apin szolgáltathatják, ha akarják. Ennek annyi előnye lehetne, hogy ebből lejöhetnének útpontok is, de ez egy külön téma. A lényeg az lenne benne, hogy ha ismétlődés van a turistautak.hu-n, akkor azt nem töltenénk át ismételten, mivel a már áttöltöttek sorra egészítenék ki az ismétlődés kiszűrésére használt adatbázist. Megjegyzem, hogy már most is lehet egy csomó ismétlődés az osm szerveren is, azt is kimutathatnánk mellékesen. [[#7]](https://github.com/kolesar-andras/turistautak-osm-track-import/issues/7)

8. Kitaláljuk a feltöltés címkéit. Ennek megvitatása is eltart egy ideig. Már önmagában az is, hogy milyen felhasználónévvel menjen fel. Én például utálnám ha a közösbe mennének a nyomvonalaim, gondolom más is van így ezzel. Megkérdezném a felhasználókat, hogy kik azok akik nem szeretnék a közös felhasználó általi feltöltést és megadnám nekik a lehetőséget, hogy maguk töltsék fel a csomagot, amelyet valahogyan eljuttatunk hozzájuk, vagy megkérjük őket, hogy a feltöltés idejére adjanak egy átmeneti osm jelszót. [[#8]](https://github.com/kolesar-andras/turistautak-osm-track-import/issues/8)

9. Feltöltés a szerverre. Itt lehet egy csomó gond: túl nagy a fájl, túlterhelt a szerver, leakad a net. Ezeket mind kezelni kell, újra próbálkozni, tárolni hogy mi sikerült és mi nem. Fontos eldöntenünk, hogy mi kerüljön egy feltöltésbe és milyem formában. Egy turistautak.hu nyomvonal-adatlapon lehet több fájl, ezek egyesével lesznek .gpx állományok. Jó-e az, hogy egy fájl esetén .gpx.gz, több fájl esetén .zip csomagba kerüljön a .gpx? Vagy van jobb ötletetek? [[#9]](https://github.com/kolesar-andras/turistautak-osm-track-import/issues/9)

Létrehoztam a fenti kilenc ponthoz egy-egy [issue](https://github.com/kolesar-andras/turistautak-osm-track-import/issues)-t, ott várom észrevételeiteket, javaslataitokat. Ha fejlesztőként fork és pull request formájában csatlakozhatsz.

Kapcsolódó címek:

* [turistautak-osm-api](https://github.com/kolesar-andras/turistautak-osm-api)
* [turistautak.hu osm fórum](http://turistautak.hu/forum.php?id=osm)

### Telepítés

A program egy része már használható, a [parancssorban kiválasztható feladatok](https://github.com/kolesar-andras/turistautak-osm-track-import/issues/11) közül működik a download, convert és a compare.

Add ki az alábbi parancsokat. Készít magának alkönyvtárat turistautak-osm-track-import néven.

	git clone https://github.com/kolesar-andras/turistautak-osm-track-import.git
	
Ha nem lenne git a gépeden, Debian alapú Linuxon (például Ubuntun) így tudod telepíteni:
	
	sudo apt-get install git
	
Ha ez valamiért nem megy, nem szükséges git, [letöltheted .zip fájlként](https://github.com/kolesar-andras/turistautak-osm-track-import/archive/master.zip) is.

A program futásához szükség van külső összetevőkre, amelyeket a Composer nevű PHP csomagkezelő intéz. Amíg ez nincs meg, a program kiírja hogy mire van szüksége:

	Setup incomplete, please run the following command:

	composer update

	If you do not have composer, run this command to get it:

	php -r "readfile('https://getcomposer.org/installer');" | php

	This installs itself as composer.phar in the current directory, then call this way:

	php composer.phar update

Ezután már hívható az alábbi formában:

	php track.php

