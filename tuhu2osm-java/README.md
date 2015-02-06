tuhu2osm (Java)
============================

#Leírás

A [turistautak.hu](http://turistautak.hu/)-n [OSM](http://www.openstreetmap.org) számára felajánlott tracklogok áttöltését segítő program.

Letölti a paraméterben megadott turistautak.hu felhasználó összes trackjét, gpx-be konvertálja, szükség szerint zgip csomagot készít belőle, majd feltölti OSM-re, megadva a turistautak.hu track adatlapjáról kiolvasott metaadatokat.

#Használat

tuhu2osm [kapcsoló érték|kapcsoló]

    -u, --user
      Importálni kívánt TuHu felhasználó neve
    -f, --start_date
      Kezdő dátum yyyy-mm-dd formátumban. Csak az ezután feltöltött trackek lesznek áttöltve
    (-t, --end_date
      Vég dátum yyyy-mm-dd formátumban. Csak az ennél korábban feltöltött trackek lesznek áttöltve.) mégnem működik
    --osm_name
      feltöltéshez használt OSM felhasználónév (default: tuhu2osmBot)
    --osm_pwd
      feltöltéshez használt OSM jelszó
    --tuhu_name
      letöltéshez használt TuHu felhasználónév (default: osmtuhu)
    --tuhu_pwd
      letöltéshez használt TuHu felhasználó jelszava
    -o, --only_download
      Csak letöltés és konvertálás, OSM-re feltöltés nélkül
    -d, --dev
      Fejlesztői OSM API használata, tehát a feltöltés nem élseben megy
    -h, --help
      Súgó
