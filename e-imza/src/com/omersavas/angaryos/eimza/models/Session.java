/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.omersavas.angaryos.eimza.models;

import com.google.gson.internal.LinkedTreeMap;
import com.omersavas.angaryos.eimza.helpers.GeneralHelper;
import com.omersavas.angaryos.eimza.helpers.Security;
import com.omersavas.angaryos.eimza.helpers.Log;
import com.omersavas.angaryos.eimza.helpers.SigningSmartCardManager;
import com.omersavas.angaryos.eimza.helpers.SigningTestConstants;
import com.omersavas.angaryos.eimza.helpers.Encryption;
import java.awt.Frame;
import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.io.UnsupportedEncodingException;
import java.math.BigInteger;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URI;
import java.net.URL;
import java.net.URLEncoder;
import static java.nio.charset.StandardCharsets.UTF_8;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.security.InvalidKeyException;
import java.security.NoSuchAlgorithmException;
import java.security.cert.CertificateException;
import java.security.cert.X509Certificate;
import java.util.ArrayList;
import java.util.Base64;
import java.util.List;
import javax.crypto.BadPaddingException;
import javax.crypto.IllegalBlockSizeException;
import javax.crypto.NoSuchPaddingException;
import javax.net.ssl.HostnameVerifier;
import javax.net.ssl.HttpsURLConnection;
import javax.net.ssl.SSLContext;
import javax.net.ssl.SSLSession;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;
import javax.xml.ws.Response;
import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.HttpVersion;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.conn.ssl.NoopHostnameVerifier;
import org.apache.http.conn.ssl.SSLContextBuilder;
import org.apache.http.conn.ssl.TrustAllStrategy;
import org.apache.http.entity.ContentType;
import org.apache.http.entity.mime.HttpMultipartMode;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.entity.mime.MultipartEntityBuilder;
import org.apache.http.entity.mime.content.ContentBody;
import org.apache.http.entity.mime.content.FileBody;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.params.CoreProtocolPNames;
import org.apache.http.util.EntityUtils;
import tr.gov.tubitak.uekae.esya.api.smartcard.pkcs11.SmartOp;

/**
 *
 * @author omers
 */
public class Session {

    public String url = "https://192.168.10.185/", apiBaseUrl;
    public String tokenPath = "files/token.ang",  mailPath = "files/mail.ang",  urlPath = "files/url.ang";
    public String token = "";
    public LinkedTreeMap loggedInUserInfo = null;

    public Session() throws IOException {
        File f = new File(urlPath);
        if(f.exists()) this.url = (new String(Files.readAllBytes(Paths.get(this.urlPath))));
        
        this.apiBaseUrl = this.url + "api/v1/";
    }
    
    private boolean fillToken(String t) throws InvalidKeyException, IllegalBlockSizeException, BadPaddingException, FileNotFoundException, UnsupportedEncodingException
    {
        try {
            
            token = t;
            loggedInUserInfo = getLoggedInUserInfo();
            
            String tc = GeneralHelper.getSigning().getTCNumber();
            String userTc = ((LinkedTreeMap)loggedInUserInfo.get("user")).get("tc").toString();
            if(!userTc.equals(tc))
            {
                GeneralHelper.showMessageBox("Cihaz başka bir kullanıcıya ait!");
                return false;
            }
            
            Encryption enc = GeneralHelper.getEncryption();
        
            File f = new File(tokenPath);
            if(f.exists()) f.delete();
            else f.createNewFile();

            BigInteger serial = GeneralHelper.getSigning().getSerialNumber();
            if(false && serial == BigInteger.ZERO)
            {
                GeneralHelper.showMessageBox("Cihaz takılı değil!");
                return false;
            }
            
            token = t;
            t += "|"+serial;
            t += "|"+tc;
            
            PrintWriter writer = new PrintWriter(tokenPath, "UTF-8");
            writer.print(enc.encode(t));
            writer.close();

            return true;
        } catch (Exception e) {
            Log.send(e);
            return false;
        }       
    }    
    
    public boolean controlRememberUser() throws InvalidKeyException, IllegalBlockSizeException, BadPaddingException{
        try {
            
            if(SmartOp.getCardTerminals().length == 0) return false;
            
            File f = new File(mailPath);
            if(!f.exists()) return false;
            
            String email = (new String(Files.readAllBytes(Paths.get(mailPath))));
            GeneralHelper.createEncryptionObject(email);
            Encryption enc = GeneralHelper.getEncryption();
        
            
            f = new File(tokenPath);
            if(!f.exists()) return false;            
            
            String t = enc.decode(new String(Files.readAllBytes(Paths.get(tokenPath))));

            if(t.length() == 0)
            {
                f.delete();
                return false;
            }
                
            String[] temp = t.split("\\|");
            if(temp.length != 3) 
            {
                f.delete();
                return false;
            }
            
            BigInteger rememberedSerial = new BigInteger(temp[1]);
            String rememberedTc = temp[2];
            
            BigInteger currentSerial = GeneralHelper.getSigning().getSerialNumber();
            String currentTc = GeneralHelper.getSigning().getTCNumber();
            
            if(!rememberedSerial.equals(currentSerial) | !rememberedTc.equals(currentTc))
            {
                GeneralHelper.showMessageBox("Bu kullanıcı hatırlanmıyor!");
                f.delete();
                return false;
            }
            
            return fillToken(temp[0]);
            
        } catch (Exception e) {
            Log.send(e);
            return false;
        }
    }

    private String cleanQuotes(String s)
    {
        return s.replaceAll("'", "").replaceAll("\"", "");
    }
    
    public String httpGetBasic(String u) throws MalformedURLException, IOException
    {
        try {
            
            TrustManager[] trustAllCerts = new TrustManager[] {
                new X509TrustManager() {
                    public java.security.cert.X509Certificate[] getAcceptedIssuers() {
                        return null;
                    }
                    public void checkClientTrusted(X509Certificate[] certs, String authType) {
                    }
                    public void checkServerTrusted(X509Certificate[] certs, String authType) {
                    }
                }
            };
            
            SSLContext sc = SSLContext.getInstance("SSL");
            sc.init(null, trustAllCerts, new java.security.SecureRandom());
            HttpsURLConnection.setDefaultSSLSocketFactory(sc.getSocketFactory());

            HostnameVerifier allHostsValid = new HostnameVerifier() {
                public boolean verify(String hostname, SSLSession session) {
                    return true;
                }
            };
            
            HttpsURLConnection.setDefaultHostnameVerifier(allHostsValid);
            
            URL url = new URL(u);
            HttpURLConnection conn = (HttpURLConnection) url.openConnection();
            conn.setRequestMethod("GET");
            
            BufferedReader rd = new BufferedReader(new InputStreamReader(conn.getInputStream()));
            
            StringBuilder result = new StringBuilder();
            String line;
            while ((line = rd.readLine()) != null) {
               result.append(line);
            }
            rd.close();

            return result.toString();
            
        } catch (Exception e) {
            GeneralHelper.showMessageBox("Sunucuya erişilemedi! Lütfen sonra tekrar deneyin.");
            return null;
        }
    }
    
    public LinkedTreeMap httpGet(String u) throws MalformedURLException, IOException
    {
        try {
            String json = this.httpGetBasic(u);
            LinkedTreeMap r = GeneralHelper.jsonDecode(json);
            
            String status = r.get("status").toString();
            LinkedTreeMap d = (LinkedTreeMap)r.get("data");

            if(status.equals("success")) return d;
            else this.writeServerMessage(d.get("message").toString());
            
            return null;
            
        } catch (Exception e) {
            GeneralHelper.showMessageBox("Sunucuya erişilemedi! Lütfen sonra tekrar deneyin.");
            return null;
        }
    }
    
    public LinkedTreeMap httpPost(String u, List<NameValuePair> data) {
        
        try {
            HttpClient httpclient = HttpClients
                                        .custom()
                                        .setSSLContext(new SSLContextBuilder().loadTrustMaterial(null, TrustAllStrategy.INSTANCE).build())
                                        .setSSLHostnameVerifier(NoopHostnameVerifier.INSTANCE)
                                        .build();
            
            HttpPost httppost = new HttpPost(u);

            httppost.setEntity(new UrlEncodedFormEntity(data, "UTF-8"));

            HttpResponse response = httpclient.execute(httppost);
            HttpEntity entity = response.getEntity();

            if (entity != null) {
                String json = EntityUtils.toString(entity);
                LinkedTreeMap r = GeneralHelper.jsonDecode(json);
            
                String status = r.get("status").toString();
                LinkedTreeMap d = (LinkedTreeMap)r.get("data");
                
                if(status.equals("success")) return d;
                else this.writeServerMessage(d.get("message").toString());
            }
            
            return null;
            
        } catch (Exception e) {
            GeneralHelper.showMessageBox("Sunucuya erişilemedi! Lütfen sonra tekrar deneyin.");
            return null;
        }
    }
    
    private void writeServerMessage(String m)
    {
        switch(m)
        {
            case "mail.or.password.incorrect":
                m = "Mail yada şifre yanlış!";
                break;
        }
        
        GeneralHelper.showMessageBox(m);
    }
    
    private LinkedTreeMap getLoggedInUserInfo()
    {
        try {
            return httpGet(apiBaseUrl + token + "/getLoggedInUserInfo");
        } catch (Exception e) {
            Log.send(e);
        }
        
        return null;
    }
    
    public boolean loginWithMail(String email, String password) throws IOException, InvalidKeyException, IllegalBlockSizeException, BadPaddingException, NoSuchAlgorithmException, NoSuchPaddingException {
        if(!Security.tryLogin()){
            GeneralHelper.showMessageBox("Yeteri kadar deneme yaptınız! İzin verilmiyor...");
            return false;
        }
        
        email = cleanQuotes(email);
        password = cleanQuotes(password);
        
        String u = apiBaseUrl + "login";

        List<NameValuePair> data = new ArrayList<NameValuePair>(2);
        data.add(new BasicNameValuePair("email", email));
        data.add(new BasicNameValuePair("password", password));
        data.add(new BasicNameValuePair("clientInfo", "{type: 'pc', app: 'e-imza.jar'}"));
            
        LinkedTreeMap r = httpPost(u, data);
        if(r == null) return false;
        
        this.writeUserMailToFile(email);  
        GeneralHelper.createEncryptionObject(email);
        
        if(fillToken(r.get("token").toString()))
            return true;
        else{
            GeneralHelper.showMessageBox("Giriş yapıldı ama token oluşturulamadı! Yöneticiye başvurun.");
            return false;
        }
        
    }
    
    public boolean writeUserMailToFile(String email) throws FileNotFoundException, UnsupportedEncodingException
    {
        try {
            File f = new File(mailPath);
            if(f.exists()) f.delete();
            else f.createNewFile();

            PrintWriter writer = new PrintWriter(mailPath, "UTF-8");
            writer.print(email);
            writer.close();
            
            return true;
        } catch (Exception e) {
            Log.send(e);
            return false;
        }
    }

    public void logoutAndExit() {
        File f = new File(tokenPath);
        if(f.exists()) f.delete();
        System.exit(0); 
    }

    public boolean uploadSign(LinkedTreeMap eSign, String path) {
        
        try {
            String id = eSign.get("id").toString().replace(".0", "");
            
            String u = this.apiBaseUrl + this.token + "/tables/e_signs/"+id+"/update";

            HttpClient httpClient = HttpClients
                                        .custom()
                                        .setSSLContext(new SSLContextBuilder().loadTrustMaterial(null, TrustAllStrategy.INSTANCE).build())
                                        .setSSLHostnameVerifier(NoopHostnameVerifier.INSTANCE)
                                        .build();

            HttpPost httpPost = new HttpPost(u);

            //File file = new File(path);
            //FileBody fileBody = new FileBody(file);

            /*MultipartEntity reqEntity = new MultipartEntity(HttpMultipartMode.BROWSER_COMPATIBLE);
            reqEntity.addPart("sign_file[]", fileBody);
            
            
            httpPost.setEntity(reqEntity);

            List<NameValuePair> data = new ArrayList<NameValuePair>(2);
            data.add(new BasicNameValuePair("state", "1"));
            data.add(new BasicNameValuePair("sign_at", GeneralHelper.getTimeStamp("yyyy-MM-dd HH:mm:ss")));
            data.add(new BasicNameValuePair("sign_file_old", null));
            data.add(new BasicNameValuePair("column_set_id", GeneralHelper.formColumnSetId+""));
            data.add(new BasicNameValuePair("id", id));
            
            httpPost.setEntity(new UrlEncodedFormEntity(data, "UTF-8"));*/
            MultipartEntityBuilder builder = MultipartEntityBuilder.create();
            builder.addTextBody("state", "1", ContentType.TEXT_PLAIN);
            builder.addTextBody("sign_at", GeneralHelper.getTimeStamp("yyyy-MM-dd HH:mm:ss"), ContentType.TEXT_PLAIN);
            builder.addTextBody("sign_file_old", "", ContentType.TEXT_PLAIN);
            builder.addTextBody("column_set_id", GeneralHelper.formColumnSetId+"", ContentType.TEXT_PLAIN);
            builder.addTextBody("id", id, ContentType.TEXT_PLAIN);

            File f = new File(path);
            builder.addBinaryBody( "sign_file[]", new FileInputStream(f), ContentType.create("application/pkcs7-signature"), f.getName() );

            HttpEntity multipart = builder.build();
            
            httpPost.setEntity(multipart);
            
            
            
            HttpResponse response = httpClient.execute(httpPost);            
            HttpEntity entity = response.getEntity();

            if (entity == null) return false;
            
            String json = EntityUtils.toString(entity);
            LinkedTreeMap r = GeneralHelper.jsonDecode(json);

            String status = r.get("status").toString();

            return status.equals("success");
            
        } catch (Exception e) {
            Log.send(e);
            return false;
        }
    }
}
