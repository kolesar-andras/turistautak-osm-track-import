import java.util.ArrayList;
import java.util.Date;

public class TrackClass {

	public static class FileData{
		private String url;
		private String path;
		private String name;
		private String ext;
		private boolean isConverted = false;

		public String getPath() {
			return path;
		}
		public void setPath(String path) {
			this.path = path;
		}

		public String getURL() {
			return url;
		}
		public void setURL(String url) {
			this.url = url;
		}

		public String getName() {
			return name;
		}
		public void setName(String name) {
			this.name = name;
		}

		public String getExt() {
			return ext;
		}
		public void setExt(String ext) {
			this.ext = ext;
		}

		public boolean isConverted() {
			return isConverted;
		}
		public void setConvertability(boolean c) {
			this.isConverted = c;
		}

		public FileData() {

		}
	}


	public int id = -1;
	private String ext = "";
	private String name = "";
	private Date uploadDate = null;
	private ArrayList<String> fileURL;
	private ArrayList<FileData> files;
	private String newPath = "";
	private String region = "";
	private String gpsType = "";
	private String trackingType = "";
	private String loggingMode = "";
	private String details = "";
	private boolean noJam = false;
	private boolean barometer = false;
	private boolean goodSatelite = false;
	private boolean noSegments = false;
	private boolean twoWay = false;
	private boolean trackedCrossings = false;
	private boolean croped = false;
	private boolean wellNamed = false;
	private boolean hasDraft = false;
	private boolean crossingsMarked = false;

	public TrackClass(int id) {
		this.id = id;
		files = new ArrayList<FileData>();
	}

	public TrackClass(int id, Date uploadDate) {
		this.id = id;
		this.uploadDate = uploadDate;
		files = new ArrayList<FileData>();
	}

	public FileData getFile(int i) {
		if (getFileCount() <= i)	//if i out of bounds
			return null;
		return (FileData) files.toArray()[i];
	}
	public int getFileCount(){
		return files.size();
	}
	public int getConvertedFileCount(){
		int count = 0;
		for (int i=0; i<getFileCount(); ++i) {
			if (getFile(i).isConverted()) {
				++count;
			}
		}
		return count;
	}
	public void addFile(FileData file) {
		files.add(file);
	}
	public void addFile(String path, String url, String name, String ext) {
		FileData file = new FileData();
		file.path = path;
		file.url = url;
		file.name = name;
		file.ext = ext;
		files.add(file);
	}
	public void removeFile(int i){
		files.remove(i);
	}

	public String getNewPath() {
		return newPath;
	}
	public void setNewPath(String newPath) {
		this.newPath = newPath;
	}

	public String getExt() {
		return ext;
	}
	public void setExt(String ext) {
		this.ext = ext;
	}

	public int getId() {
		return id;
	}
	public void setId(int id) {
		this.id = id;
	}

	public String getName() {
		return name;
	}
	public void setName(String name) {
		this.name = name;
	}

	public Date getUploadDate() {
		return uploadDate;
	}
	public void setUploadDate(Date uploadDate) {
		this.uploadDate = uploadDate;
	}

/*
	public String[] getFileURLs() {
		return (String[]) fileURL.toArray();
	}
	public void addFileURL(String fileURL) {
		this.fileURL.add(fileURL);
	}
*/


	//Other details:
	public boolean isBarometer() {
		return barometer;
	}
	public void setBarometer(boolean barometer) {
		this.barometer = barometer;
	}

	public boolean isCroped() {
		return croped;
	}
	public void setCroped(boolean croped) {
		this.croped = croped;
	}

	public boolean isCrossingsMarked() {
		return crossingsMarked;
	}
	public void setCrossingsMarked(boolean crossingsMarked) {
		this.crossingsMarked = crossingsMarked;
	}

	public String getDetails() {
		return details;
	}
	public void setDetails(String details) {
		this.details = details;
	}

	public boolean isGoodSatelite() {
		return goodSatelite;
	}
	public void setGoodSatelite(boolean goodSatelite) {
		this.goodSatelite = goodSatelite;
	}

	public String getGpsType() {
		return gpsType;
	}
	public void setGpsType(String gpsType) {
		this.gpsType = gpsType;
	}

	public boolean isHasDraft() {
		return hasDraft;
	}
	public void setHasDraft(boolean hasDraft) {
		this.hasDraft = hasDraft;
	}

	public String getLoggingMode() {
		return loggingMode;
	}
	public void setLoggingMode(String loggingMode) {
		this.loggingMode = loggingMode;
	}

	public boolean isNoJam() {
		return noJam;
	}
	public void setNoJam(boolean noJam) {
		this.noJam = noJam;
	}

	public boolean isNoSegments() {
		return noSegments;
	}
	public void setNoSegments(boolean noSegments) {
		this.noSegments = noSegments;
	}

	public String getRegion() {
		return region;
	}
	public void setRegion(String region) {
		this.region = region;
	}

	public boolean isTrackedCrossings() {
		return trackedCrossings;
	}
	public void setTrackedCrossings(boolean trackedCrossings) {
		this.trackedCrossings = trackedCrossings;
	}

	public String getTrackingType() {
		return trackingType;
	}
	public void setTrackingType(String trackingType) {
		this.trackingType = trackingType;
	}

	public boolean isTwoWay() {
		return twoWay;
	}
	public void setTwoWay(boolean twoWay) {
		this.twoWay = twoWay;
	}

	public boolean isWellNamed() {
		return wellNamed;
	}
	public void setWellNamed(boolean wellNamed) {
		this.wellNamed = wellNamed;
	}
}
