import java.net.*;
import java.io.*;
import java.util.regex.*;
import java.util.zip.*;
import java.util.Date;
import java.util.ArrayList;
import java.text.DateFormat;
import java.text.SimpleDateFormat;

public class tuhu2osm {
	public static final String VERSION = "1.3";
	public static String userId, userName = "";
	public static Date startDate, endDate = null;
	public static String TuHuName = "osmtuhu", TuHuPwd = "";
	public static String OSMName  = "tuhu2osmBot", OSMPwd  = "";
	public static String osmAPI = "http://api.openstreetmap.org/api/0.6/gpx/create";
	public static final String osmDevAPI = "http://api06.dev.openstreetmap.org/api/0.6/gpx/create";
	public static boolean onlyDownload = false;
	public static String workingDir, tempDir, separator, GPSBabelPath;
	public static ArrayList<TrackClass> tracks;
	
	public static void main(String[] args) throws Exception {

		int i = 0;

		DateFormat isoDateFormat = new SimpleDateFormat("yyyy-mm-dd");
		
		while (i<args.length) {
			if (args.length > i+1 && !args[i+1].substring(0,1).equals("-")) {
				String value = args[i+1];
				switch(args[i]) {
					case "-u":
					case "--user":
						userName=value;
						break;
					case "-f":
					case "--start_date":
						try {
							startDate=isoDateFormat.parse(value);
						} catch (Exception e) {
							System.out.println("Hibás dátumformátum: "+value);
							System.out.println("A dátumot yyyy-mm-dd formátumban add meg!");
							System.exit(1);
						}
						break;
					case "-t":
					case "--end_date":
						try {
							endDate=isoDateFormat.parse(value);
						} catch (Exception e) {
							System.out.println("Hibás dátumformátum: "+value);
							System.out.println("A dátumot yyyy-mm-dd formátumban add meg!");
							System.exit(1);
						}
						break;
					case "--osm_name":
						OSMName=value;
						break;
					case "--osm_pwd":
						OSMPwd=value;
						break;
					case "--tuhu_name":
						TuHuName=value;
						
						break;
					case "--tuhu_pwd":
						TuHuPwd=value;
						break;
				}
				i=i+2;
			} else {
				switch (args[i]) {
					case "-d":
					case "--dev":
						osmAPI = osmDevAPI;
						System.out.println("Using dev OSM API: " + osmAPI);
						break;
					case "-o":
					case "--only_download":
						onlyDownload = true;
						break;
					case "-h":
					case "--help":

System.out.println("tuhu2osm - Version " + VERSION);
System.out.println("");
System.out.println("A turistautak.hu-n OSM számára felajánlott tracklogok áttöltését segítő program.");
System.out.println("Amennyiben a kiválaszott TuHu felhasználó ehhez hozzájárult, a program az összes tracklogját áttölti.");
System.out.println("");
System.out.println("Használat: tuhu2osm [kapcsoló érték|kapcsoló]");
System.out.println("-u, --user		Importálni kívánt TuHu felhasználó neve");
System.out.println("-f, --start_date	Kezdő dátum yyyy-mm-dd formátumban. Csak az ezután feltöltött trackek lesznek áttöltve.");
System.out.println("(-t, --end_date		Vég dátum yyyy-mm-dd formátumban. Csak az ennél korábban feltöltött trackek lesznek áttöltve.) mégnem működik");
System.out.println("--osm_name		feltöltéshez használt OSM felhasználónév (default: tuhu2osmBot)");
System.out.println("--osm_pwd		feltöltéshez használt OSM jelszó");
System.out.println("--tuhu_name		letöltéshez használt TuHu felhasználónév (default: osmtuhu)");
System.out.println("--tuhu_pwd		letöltéshez használt TuHu felhasználó jelszava");
System.out.println("-o, --only_download	Csak letöltés és konvertálás, OSM-re feltöltés nélkül");
System.out.println("-d, --dev		Fejlesztői OSM API használata, tehát a feltöltés nem élseben megy");
System.out.println("-h, --help		Ez a súgó");
System.exit(0);
						break;
				}
				i=i+1;
			}
		}

		InputStreamReader converter = new InputStreamReader(System.in);
		BufferedReader consoleReader = new BufferedReader(converter);
		separator = System.getProperty("file.separator");
		workingDir = System.getProperty("user.dir") + separator;
		tempDir = workingDir + "temp" + separator ;
		new File(tempDir).mkdirs();
		
		//Locate GPSBabel installation
		if (System.getProperty("os.name").equals("Linux")){
			Process p = Runtime.getRuntime().exec("which gpsbabel");
			if (p.waitFor() == 0) {
				GPSBabelPath = "gpsbabel";
			}else{
				System.err.println("Kérlek telepítsd a gpsbabel csomagot!");
				System.exit(1);
			}
			
		}else {
			GPSBabelPath = "C:\\Program Files\\GPSBabel\\gpsbabel.exe";
			if (!(new File(GPSBabelPath)).exists()){
				GPSBabelPath = "C:\\Program Files (x86)\\GPSBabel\\gpsbabel.exe";
				if (!(new File(GPSBabelPath)).exists()){
					System.out.println("Kerlek add meg a GPSBabel eleresi utvonalat:");
					GPSBabelPath = consoleReader.readLine();
					if (!(new File(GPSBabelPath)).exists()){
						System.err.println("A GPSBabel nem talalahato a megadott utvonalon!");
						System.exit(1);
					}
				}
			}
			GPSBabelPath = "\""+GPSBabelPath+"\"";
		}
		
		//Read username
		if (userName.isEmpty()) {
			System.out.println("Importalando felhasznalo:");
			userName = consoleReader.readLine();
		}

		//Finding id of the given user
		if (findUserId())
			processTrackList();
   }

	public static boolean findUserId(){
		String[][] charMap = {{"(?i) ","%20"}, {"(?i)á","a"}, {"(?i)é","e"}, {"(?i)í","i"}, {"(?i)ó","o"}, {"(?i)ö","o"}, {"(?i)ő","o"}, {"(?i)ú","u"}, {"(?i)ü","u"}, {"(?i)ű","u"}};
		String tuhuCompatibleUserName = userName;
		for (int i=0; i<charMap.length; ++i){
			tuhuCompatibleUserName = tuhuCompatibleUserName.replaceAll(charMap[i][0], charMap[i][1]);
		}

		HttpHandler hh = new HttpHandler("http://geocaching.hu/users.geo?nick=" + tuhuCompatibleUserName);
		String temp = hh.readURL();
		
		if (temp.indexOf(" a megadott felhasználó") > -1) {
			System.out.println("Nincs ilyen felhasznalo!");
		}else {
			Pattern p = Pattern.compile("http:\\/\\/turistautak\\.hu\\/tracks\\.php\\?owner\\=([0-9]*)");
			Matcher m = p.matcher(temp);
			if (!m.find()) {
				System.out.println("A felhasznalonak nincsenek feltoltott utvonalai!");
			}else {
				userId =  m.group(1);
				if (	userName.equals(TuHuName) ||
						temp.indexOf("hozzájárulok")>-1 && 
						(temp.indexOf("OSM")>-1 || temp.indexOf("OpenStreetMap")>-1 || temp.indexOf("openstreetmap")>-1 )
					) {
					return true;
				}else {
					System.out.println("Nem találhato a felhasználó adatlapján a jogi nyilatkozat!");
				}
			}
		}
		return false;
	}
	
	public static void processTrackList(){
		HttpHandler hh = new HttpHandler("http://turistautak.hu/tracks.php?egylapon=10000&owner=" + userId);
		String pageText = hh.readURL();

		Pattern p = Pattern.compile(
			"tracks\\.php\\?id\\=([0-9]*).*?>([0-9]{4}\\.[0-9]{2}\\.[0-9]{2})<\\/td>"
			);
		Matcher m = p.matcher(pageText);

		tracks = new ArrayList<TrackClass>();

		DateFormat df = new SimpleDateFormat("yyyy.mm.dd");
		while (m.find()) {
			TrackClass track;
			if (startDate == null) {
				try {
					Date isoDate = df.parse(m.group(2));
					track = new TrackClass( Integer.parseInt(m.group(1)), isoDate );
				} catch (Exception e) {
					track = new TrackClass( Integer.parseInt(m.group(1)) );
				}
				tracks.add(track);
			} else{
				try {
					Date isoDate = df.parse(m.group(2));
					if (isoDate.after(startDate)) {
						track = new TrackClass( Integer.parseInt(m.group(1)), isoDate );
						tracks.add(track);
					}
				} catch (Exception e) {}
			}
		}
		System.out.print(tracks.size() + " tracket találtam. Szeretnéd "+ (onlyDownload ? "lementeni" : "importálni") +"? (i/n) ");
		
		try {
			InputStreamReader converter = new InputStreamReader(System.in);
			BufferedReader consoleReader = new BufferedReader(converter);
			
			String in = consoleReader.readLine();

			if (!in.equals("i")){
				System.exit(0);
			}
		}catch(Exception e){System.exit(1);}

		System.out.println("");

		int downloadCounter = 0;
		int successCounter = 0;
		final int len = tracks.size();
		for (int i=len-1; i>=0; --i) {
			tracks.add(i, getTrackDetails(tracks.get(i)));

			for (int j=0; j<tracks.get(i).getFileCount(); ++j){

				if (downloadFile(tracks.get(i).getFile(j))){
					++downloadCounter;
					convertFile(tracks.get(i).getFile(j));
				}else{
					tracks.get(i).removeFile(j);
				}
			}

			if (!onlyDownload) {
				if (prepareAndUploadFiles(tracks.get(i))) {
					++successCounter;
				}
			}
		}
		System.out.println("----------------------------------------------------");
		if (!onlyDownload) {
			System.out.println(userName + " " + len + " nyomvonalából " + successCounter + " sikeresen áttöltve OSM-be." );
		} else {
			System.out.println(userName + " " + len + " nyomvonalából " + downloadCounter + " sikeresen letöltve." );
		}
	}
	
	public static TrackClass getTrackDetails(TrackClass track){
		// Downloading page source
		HttpHandler hh = new HttpHandler("http://turistautak.hu/tracks.php?id=" + track.getId());
		String pageText = hh.readURL();

		// Prepare page source for parsing
		String filteredText = pageText;
		filteredText = filteredText.replaceAll("<br />", ", ");
		filteredText = filteredText.replaceAll("<b>", "");
		filteredText = filteredText.replaceAll("</b>", "");
		filteredText = filteredText.replaceAll("<[^<>]*>", "Ě");	// Replacing html tags to a special character
		
		//Extract track datas using regexp
		track.setName(getTrackData("Track neve:[Ě]{2,3}([^Ě]*)Ě", filteredText));
		track.setRegion(getTrackData("Tájegység:[Ě]{2,3}([^Ě]*)Ě", filteredText));
		track.setGpsType(getTrackData("GPS típusa:[Ě]{2,3}([^Ě]*)Ě", filteredText));
		track.setTrackingType(getTrackData("Bejárás módja:[Ě]{2,3}([^Ě]*)Ě", filteredText));
		track.setLoggingMode(getTrackData("Logolás jellege:[Ě]{2,3}([^Ě]*)Ě", filteredText).replace(" ()", "")+" log");
		track.setNoJam(getTrackDataBool("nem volt GPS-vételt zavaró körülmény", filteredText));
		track.setBarometer(getTrackDataBool("kalibrált barométeres magasság-adatok", filteredText));
		track.setGoodSatelite(getTrackDataBool("végig jó műholdállás", filteredText));
		track.setNoSegments(getTrackDataBool("nem szakad a track menet közben", filteredText));
		track.setTwoWay(getTrackDataBool("oda-vissza track a teljes útvonalról", filteredText));
		track.setTrackedCrossings(getTrackDataBool("útkereszteződések bejárva \\(legalább", filteredText));
		track.setCroped(getTrackDataBool("felesleges részek levágva", filteredText));
		track.setWellNamed(getTrackDataBool("trackek és útpontok elnevezései beszédesek", filteredText));
		track.setHasDraft(getTrackDataBool("vázlat készült a csomópontokról", filteredText));
		track.setCrossingsMarked(getTrackDataBool("minden lehetséges csomópont megjelölve", filteredText));
		track.setDetails(getTrackData("ĚĚ([^Ě]*)Ě+A feltöltött fájl", filteredText));
		

		String[][] charMap = {{" ","_"}, {"-","_"}, {"\\?",""}, {"\\\\",""}, {"\\/",""}, {":",""}, {"\\*",""}, {"\"","<"}, {">",""}, {"|",""}};

		Pattern p = Pattern.compile("<a href=\\\"(file.php\\?dir=upload&id=[0-9]*&file=[^\\\"]*)\\\">([^\\/]*)<br>", Pattern.CASE_INSENSITIVE);			
		Matcher m = p.matcher(pageText);

		// Counting files and init array
		int count=0;
		while (m.find())
			++count;

		TrackClass.FileData[] files = new TrackClass.FileData[count];
		m.reset();

		// Fill array with matcher results
		int i=0;
		while (m.find()){
			files[i] = new TrackClass.FileData();
			files[i].setURL(m.group(1));
			files[i].setName(m.group(2).substring(0, m.group(2).lastIndexOf(".")));

			// Replace not allowed characters
			String filePath = track.getId()+"_"+track.getName()+"__"+files[i].getName();
			for (int j=0; j<charMap.length; ++j){
				filePath = filePath.replaceAll(charMap[j][0], charMap[j][1]);
			}

			// Set path and extension where file will be stored
			files[i].setPath( tempDir+separator+filePath );
			files[i].setExt( files[i].getURL().substring(files[i].getURL().lastIndexOf(".")+1) );

			track.addFile(files[i]);
			++i;
		}

		// Setting the upload file name that must contain only english characters
		String[][] charMap2 = {{"á","a"}, {"é","e"}, {"í","i"}, {"ó","o"}, {"ö","o"}, {"ő","o"}, {"ú","u"}, {"ü","u"}, {"ű","u"},{"Á","A"}, {"É","E"}, {"Í","I"}, {"Ó","O"}, {"Ö","O"}, {"Ő","O"}, {"Ú","U"}, {"Ü","U"}, {"Ű","U"}};

		String newPath = track.getId()+"_"+track.getName();
		for (i=0; i<charMap.length+charMap2.length; ++i){
			String[] k = (i<charMap.length) ? charMap[i] : charMap2[i-charMap.length];
			newPath = newPath.replaceAll(k[0], k[1]);
		}
		track.setNewPath(newPath);

		return track;
	}

	public static String getTrackData(String regex, String text){
		Pattern p = Pattern.compile(regex, Pattern.CASE_INSENSITIVE);			
		Matcher m = p.matcher(text);
		if (m.find())
			return m.group(1);
		else
			return "";
	}
	
	public static boolean getTrackDataBool(String key, String text){
		Pattern p = Pattern.compile("Ě([+-])[Ě]{2,3}"+key, Pattern.CASE_INSENSITIVE);
		Matcher m = p.matcher(text);
		if (m.find() && m.group(1).equals("+"))
			return true;
		else
			return false;
	}
	
	public static boolean downloadFile(TrackClass.FileData file){
		try{
			HttpHandler hh = new HttpHandler(
				"http://turistautak.hu/" + file.getURL() + "&username="+TuHuName+"&userpasswd="+TuHuPwd
			);
			hh.readURL(new BufferedOutputStream(new FileOutputStream(new File(
				file.getPath()+"."+file.getExt()
			))));
		}catch(Exception e){
			System.err.println("FIGYELEM: " +file.getName()+" fajl letoltese sikertelen! "+e.getMessage());
			return false;
		}
		return true;
	}
	
	public static boolean convertFile(TrackClass.FileData file){
		//Sample: gpsbabel -w -r -t -i gdb -f "xxxx.gdb" -o gpx -F "xxxx.gpx"
		String type = file.getExt().toLowerCase();
		if (type.equals("plt"))	type="ozi";
		if (type.equals("wpt"))	type="ozi";
		if (type.equals("g7t"))	type="g7towin";
		if (type.equals("pdb"))	type="geoniche";
		// ezek nem tudom mik, inkább nem konvertálom
		//if (type.equals("mp"))	type="mapsource";
		//if (type.equals("mps"))	type="mapsource";

		//If original file is in gpx fromat, nothing to do
		if (type.equals("gpx")) {
			return true;
		}
		
		PrintWriter pwOut = null;
		BufferedReader brIn = null;

		try {
			// Call GpsBabel to convert a file
			Process p = Runtime.getRuntime().exec(
				GPSBabelPath+
				" -w -r -t -i "+ type+
				" -f "+ file.getPath() +"."+ file.getExt()+""+
				" -o gpx "+
				" -F - "
			);
			
			pwOut = new PrintWriter(new File(file.getPath() +".gpx"));
			
			String line;
			brIn = new BufferedReader(new InputStreamReader(p.getInputStream()));
			while ((line = brIn.readLine()) != null) {
    			pwOut.println(line);
			}

			// Catch return code and read error message
			if (p.waitFor() == 1){
				System.err.println("FIGYELEM: " + file.getPath() +"."+ file.getExt() + " konvertalasa sikertelen:");
				brIn = new BufferedReader(new InputStreamReader(p.getErrorStream()));
				while ((line = brIn.readLine()) != null) {
					System.err.println("\t" + line);
				}
				file.setConvertability(false);
				return false;
			}
			
			// If successfuly converted, delete source file
			// (if not, we keep that and upload without conversation)
			new File(file.getPath() +"."+ file.getExt()).delete();
		}catch (Exception e) {
			file.setConvertability(false);
			System.err.println("HIBA: A konvertalo megnyitasa kozben hiba torent! "+e.getMessage());
		}finally {
			try{
				brIn.close();
				pwOut.close();
			} catch(IOException e) {
			}
		}
		file.setConvertability(true);
		return true;
	}
	
	private static boolean prepareAndUploadFiles(TrackClass track){

		// Prepre tags and description
		String trackingType = track.getTrackingType();
		String trackingTypeTags = "";
		if      (trackingType.indexOf("gyalog") > -1) trackingTypeTags+=", walk";
		else if (trackingType.indexOf("autóval") > -1) trackingTypeTags+=", car";
		else if (trackingType.indexOf("biciklivel") > -1) trackingTypeTags+=", bicycle";

		String tags = userName
			+((!track.getRegion().equals("other")) ? ", "+track.getRegion().replaceAll(",","") : "")
			+trackingTypeTags
			+", TuHu import, TuHu";

		String desc = "TuHu #"+ track.getId();
		if (track.getUploadDate() != null) desc += ", " + (new SimpleDateFormat("yyyy-mm-dd")).format(track.getUploadDate());
		if (trackingTypeTags.equals("") && !trackingType.equals("")) desc += ", "+ trackingType;
		if (track.isNoJam()) desc += ", zavartalan GPS jel";
		if (track.isBarometer()) desc += ", barométer";
		if (track.isGoodSatelite()) desc += ", jó műholdállás";
		if (track.isNoSegments()) desc += ", nem szakad";
		if (track.isTwoWay()) desc += ", oda-vissza";
		if (track.isTrackedCrossings()) desc += ", elágazások bejárva";
		if (track.isCroped()) desc += ", felesleg levágva";
		if (track.isWellNamed()) desc += ", beszédes nevek";
		if (track.isHasDraft()) desc += ", van vázlat";
		if (track.isCrossingsMarked()) desc += ", csomópontok jelölve";
		if (!track.getLoggingMode().equals("")) desc += ", "+track.getLoggingMode();
		if (!track.getGpsType().equals("")) desc += ", "+track.getGpsType();
		if (!track.getDetails().equals("")) desc += "; "+track.getDetails();
		
		// Unfortunately OSM accpets only 255 long descriptions so we need to cut
		if (desc.length() > 255)
			desc = desc.substring(0, 255);	


		boolean uploadSuccess = false;
		if (track.getConvertedFileCount() > 1){ // If the track has more than one file, we need to zip them
			zipFiles(track);
			// here we can upload the file
			uploadSuccess = uploadFile(track.getNewPath()+".zip", desc, tags, track.getId());
		}else if (track.getConvertedFileCount() == 1){
			for (int i=0; i<track.getFileCount(); ++i) {
				if ( track.getFile(i).isConverted()) {
					track.setNewPath(track.getNewPath()+"__"+track.getFile(i).getName());
					new File(track.getFile(i).getPath()+".gpx").renameTo(
						new File(tempDir+track.getNewPath()+".gpx")
					);
					// here we can upload the file
					uploadSuccess = uploadFile(track.getNewPath()+".gpx", desc, tags, track.getId());
				}
			}
		} else {
			if (track.getFileCount() != track.getConvertedFileCount()) {
				System.out.println("FIGYELEM: " + track.getId()
					+ " azonositoju track nem tartalmaz GPX-be konvertalhato fajlt.");
			} else {
				System.out.println("FIGYELEM: " +track.getId()
					+ " azonositoju (\"" + track.getName() + "\") rejtett track, nem tudtam letolteni!");
			}
			return false;
		}

		return uploadSuccess;
	}

	private static boolean uploadFile(String path, String description, String tags, int id) {
		try {
			System.out.print("\"" + path + "\" feltöltése...  ");
			String[][] params = {{"description",description}, {"tags",tags}, {"visibility","identifiable"}};
			File f = new File(tempDir + path);
			HttpHandler hh = new HttpHandler(osmAPI);
			hh.uploadFile(OSMName, OSMPwd, f, params);
			System.out.println("sikeres");
			// Delete temporary file
			f.delete();

		}catch(Exception e){
			System.out.println("sikertelen!");
			System.err.println( + id + " azonosítójú track nem lett importálva:\n"+e.getMessage());
			return false;
		}
		return true;
	}

	private static void zipFiles(TrackClass track) {
		final int BUFFER = 2048;
		try{
			ZipOutputStream out = new ZipOutputStream(new BufferedOutputStream(new FileOutputStream(
				tempDir+track.getNewPath()+".zip"
			)));
			byte data[] = new byte[BUFFER];

			for (int i=0; i<track.getFileCount(); ++i){
				if (track.getFile(i).isConverted()) {
					String filePath = track.getFile(i).getPath();
					String fileName = track.getFile(i).getName();
					String ext = ".gpx";

					BufferedInputStream in = new BufferedInputStream(new FileInputStream(filePath+ext), BUFFER);

					ZipEntry entry = new ZipEntry(fileName+ext);
					out.putNextEntry(entry);

					int count;
				    while((count = in.read(data, 0, BUFFER)) != -1) {
				       out.write(data, 0, count);
				    }
				    in.close();
					new File(filePath+ext).delete();
				}
			}
			out.close();
		}catch(IOException e){
			e.printStackTrace();
		}
	}
}
