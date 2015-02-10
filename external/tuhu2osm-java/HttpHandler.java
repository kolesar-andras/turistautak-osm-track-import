import java.net.*;
import java.io.*;
import java.nio.charset.Charset;
import org.apache.commons.codec.binary.Base64;
import org.apache.http.HttpEntity;
import org.apache.http.HttpException;
import org.apache.http.HttpResponse;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.mime.HttpMultipartMode;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.entity.mime.content.FileBody;
import org.apache.http.entity.mime.content.StringBody;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.EntityUtils;

public class HttpHandler {

	private URL urlres;
	private HttpURLConnection conn;
   private final int BUFFER_SIZE = 65535;
	private final String DEFAULT_CHARSET = "ISO-8859-2";
	
	public HttpHandler(String urlString){
		try{
			urlres = new URL(urlString);
		}catch(MalformedURLException e){
			System.err.println("Ervenytelen URL!");
		}
	}

	private void connect() {
		try{
			conn = (HttpURLConnection) urlres.openConnection();
		}catch(IOException e){
			new IOException("URL megnyitasa sikertelen!");
		}
	}

	public String readURL() {
		return readURL(DEFAULT_CHARSET);
	}

	public String readURL(String charset) {
		String ret = "";
		String inputLine;
		try{
			connect();
			BufferedReader reader = new BufferedReader(new InputStreamReader(conn.getInputStream(), charset));
			
			while ((inputLine = reader.readLine()) != null){
				ret += inputLine;
			}
			reader.close();
		}catch(IOException e){
			System.err.println("URL lekeres kozben hiba tortent: "+ e.getMessage());
		}
		
		return ret;
	}

	public void readURL(OutputStream out) throws Exception{
		int readByte = 0;
      byte[] buffer = new byte[BUFFER_SIZE];
		try{
			connect();
			InputStream in   = new BufferedInputStream (conn.getInputStream());
			try{
			
				while ( (readByte = in.read(buffer)) >= 0 ){
					out.write(buffer, 0, readByte);
				}
			}catch(IOException e){
				System.err.println("Fajl letoltese kozben hiba tortent: "+ e.getMessage());
			}finally{
				in.close();
				out.close();
			}
		}catch(IOException e){
				System.err.println("URL lekeres kozben hiba tortent: "+ e.getMessage());
		}
	}

	public void uploadFile(String username, String password, File file, String[][] params) throws IOException {
		DefaultHttpClient httpclient = new DefaultHttpClient();
		HttpResponse response = null;
		try{
			HttpPost post = new HttpPost(urlres.toString());

			String authString = new String(Base64.encodeBase64( new String(username+":"+password).getBytes() ));

			// Authentication
			post.addHeader("Authorization", "Basic " + authString);

			MultipartEntity entity = new MultipartEntity(HttpMultipartMode.BROWSER_COMPATIBLE, null, Charset.forName("UTF-8"));

			// Adding file
			entity.addPart("file", new FileBody(file));

			// Adding textual paramteres
			for (int i=0; i < params.length; ++i) {
				entity.addPart(params[i][0], new StringBody( params[i][1], Charset.forName("UTF-8") ));
			}
			post.setEntity(entity);

			// Sending
			response = httpclient.execute(post);

			// Reading response
			int responseCode = response.getStatusLine().getStatusCode();
			if (responseCode != 200){
				HttpEntity responseEntity = response.getEntity();
				BufferedReader reader = new BufferedReader(new InputStreamReader(responseEntity.getContent()));
				String responseMessages = "", responseLine;
				while ((responseLine = reader.readLine()) != null){
					responseMessages += responseLine + "\n";
				}
				reader.close();
				throw new RuntimeException("Hibakod: "+responseCode+", uezenet: "+ responseMessages );
			}
		}catch(Exception e) {
			throw new RuntimeException(e.getMessage());
		}
	}

}
