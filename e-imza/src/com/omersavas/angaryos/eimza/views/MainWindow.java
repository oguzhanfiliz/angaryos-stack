/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.omersavas.angaryos.eimza.views;

import com.google.gson.internal.LinkedTreeMap;
import com.omersavas.angaryos.eimza.helpers.GeneralHelper;
import com.omersavas.angaryos.eimza.helpers.Log;
import com.omersavas.angaryos.eimza.helpers.Signing;
import com.omersavas.angaryos.eimza.helpers.SigningTestConstants;
import com.omersavas.angaryos.eimza.models.Session;
import java.awt.EventQueue;
import java.io.File;
import java.io.IOException;
import java.security.InvalidKeyException;
import java.security.NoSuchAlgorithmException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Timer;
import java.util.TimerTask;
import java.util.concurrent.Callable;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.crypto.BadPaddingException;
import javax.crypto.IllegalBlockSizeException;
import javax.crypto.NoSuchPaddingException;
import javax.swing.JFrame;
import javax.swing.JOptionPane;
import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import tr.gov.tubitak.uekae.esya.api.cmssignature.CMSSignatureException;
import tr.gov.tubitak.uekae.esya.api.common.ESYAException;
import tr.gov.tubitak.uekae.esya.api.smartcard.pkcs11.SmartCardException;

/**
 *
 * @author omers
 */
public class MainWindow extends javax.swing.JFrame {

    
    
    private static boolean loadingState = false;
    private static String autoSignPath = "files/autoSign.ang";
    private static String newESignNotifyPath = "files/newESignNotify.ang";
    private static ArrayList<LinkedTreeMap> eSigns = null;
    private static LinkedTreeMap currentESign = null;
    
    
    /**
     * Creates new form MainWindow
     */
    public MainWindow() {
        try 
        {
            GeneralHelper.createEncryptionObject("angaryos");
            
            this.selfUpdateWindow();
            this.infoWindow();
            this.loginWindow();
            
            if(GeneralHelper.getSession().token.length() > 0) initComponents();
            else System.exit(0);
            
            this.setFormElementEnables(false);
            this.rememberFormData();
            
            this.eSignControlAsync(1 * 1000, false);
        }
        catch (Exception e) 
        {
            Log.send(e);
        }        
    }
    
    private void selfUpdateWindow() throws NoSuchAlgorithmException, NoSuchPaddingException, InvalidKeyException, IllegalBlockSizeException, BadPaddingException, IOException
    {
        SelfUpdateWindows suw = new SelfUpdateWindows(this, true);            
        suw.setLocationRelativeTo(null);
        GeneralHelper.setCurrentWindow(suw);
        suw.show();

        if(suw.updated){
            GeneralHelper.showMessageBox("Uygulama güncellendi! Lütfen yeniden açınız.");
            System.exit(0);
        }
    }
    
    private void infoWindow() throws NoSuchAlgorithmException, NoSuchPaddingException, InvalidKeyException, IllegalBlockSizeException, BadPaddingException
    {
        if(!GeneralHelper.showMainInfoWindowOnLoad) return;
        
        InfoWindow iw = new InfoWindow(this, true);
        iw.setLocationRelativeTo(null);
        GeneralHelper.setCurrentWindow(iw);
        iw.show();        
    }
    
    private void loginWindow() throws NoSuchAlgorithmException, NoSuchPaddingException, BadPaddingException, IllegalBlockSizeException, InvalidKeyException
    {
        LoginWindow lw = new LoginWindow(this, true);            
        lw.setLocationRelativeTo(null);
        GeneralHelper.setCurrentWindow(lw);
        lw.show();
    }
    
    private void rememberFormData()
    {
        File f = new File(autoSignPath);
        if(f.exists()) jCheckBox1.setSelected(true);
        
        f = new File(newESignNotifyPath);
        if(f.exists()) jCheckBox2.setSelected(true);
    }
    
    private void setFormElementEnables(boolean state)
    {
        jButton2.setEnabled(state);
        jButton3.setEnabled(state);
        jButton4.setEnabled(state);
        jButton5.setEnabled(state);
        jComboBox1.setEnabled(state);
    }    
    
    private void eSignControlAsync(int timeOut, boolean force)
    {
        System.out.println(timeOut);
        
        GeneralHelper.runAsync(new Callable<Void>() {
            @Override
            public Void call() throws Exception {
                MainWindow mw = GeneralHelper.getMainWindow();                
                Thread.sleep(timeOut);                
                mw.eSignControl(force);

                return null;
            }
        });
    }
    
    private void eSignControl(boolean force) throws IOException
    {
        this.loading(true);
                
        LinkedTreeMap data = this.getESignControlDataFromServer(force);
        if(data == null)
        {
            jLabel1.setText("Veri alınamadı! (" + GeneralHelper.getTimeStamp("dd/MM/yyyy HH:mm:ss")+")");
            this.loading(false);
            return;
        }
        
        this.eSignControlDataUpdated(data, force);
        this.loading(false);
    }
    
    private void eSignControlDataUpdated(LinkedTreeMap data, boolean force) throws IOException
    {
        float waitTime = Float.parseFloat(data.get("waitTime").toString());
        int count = Integer.parseInt(data.get("eSingCount").toString().replace(".0", ""));
        
        if(count == 0) this.clearESigns();
        else if(this.eSigns != null && !force) return;
        else this.getEsignsFromServer(count);
        
        int timeOut = this.getWaitTimeForESingControl(waitTime);
        if(!force) this.eSignControlAsync(timeOut, false);
    }
    
    private void getEsignsFromServer(int controlCount) throws IOException
    {
        Session session = GeneralHelper.getSession();
        String url = session.apiBaseUrl + session.token + "/tables/e_signs";

        String json = "{\"page\":1,\"limit\":\"7\",\"column_array_id\":\""+GeneralHelper.listColumnArrayId+"\",\"column_array_id_query\":\""+GeneralHelper.queryColumnArrayId;
        json += "\",\"sorts\":{\"id\":true},\"filters\":{\"sign_at\":{\"type\":100,\"guiType\":\"datetime\",\"filter\":null,\"columnName\":\"sign_at\",\"json\":\"\"},\"state\":";
        json += "{\"type\":1,\"guiType\":\"boolean\",\"filter\":true}},\"editMode\":true,\"columnNames\":[\"id\",\"table_id\",\"source_record_id\",\"column_id\",\"signed_text\",";
        json += "\"sign_at\",\"sign_file\",\"description\",\"state\",\"own_id\",\"user_id\",\"created_at\",\"updated_at\"],\"filterColumnNames\":[]}";            
        
        url += "?params="+json;
        LinkedTreeMap rt = session.httpGet(url);
        ArrayList<LinkedTreeMap> temp = (ArrayList<LinkedTreeMap>)rt.get("records");
        
        if(temp.size() != controlCount) this.eSignControlDataIsFail();
        
        if(temp.size() == 0) this.clearESigns();
        else if(this.eSignsCompare(this.eSigns, temp)) return;
        else            
        {
            this.eSigns = temp; 
            this.eSignsUpdated();
            this.setFormElementEnables(true);
            
            if(jCheckBox1.isSelected()) jButton4.doClick();
            else this.newESignsNotify();
        }
    }
    
    private void newESignsNotify()
    {
        GeneralHelper.buzzer();
            
        if(!jCheckBox2.isSelected()) return;
        
        EventQueue.invokeLater(new Runnable() {
            @Override
            public void run() {
                MainWindow mw;
                try {
                    mw = GeneralHelper.getMainWindow();

                    int sta = mw.getExtendedState() & ~JFrame.ICONIFIED & JFrame.NORMAL;

                    mw.setExtendedState(sta);
                    mw.setAlwaysOnTop(true);
                    mw.toFront();
                    mw.requestFocus();
                    mw.setAlwaysOnTop(false);
                } 
                catch (NoSuchAlgorithmException ex) { } catch (NoSuchPaddingException ex) { } 
                catch (InvalidKeyException ex) { } catch (IllegalBlockSizeException ex) { } 
                catch (BadPaddingException ex) { }
            }
        });        
    }
    
    private void eSignControlDataIsFail() throws IOException
    {
        this.getESignControlDataFromServer(true);
    }
    
    private void eSignsUpdated()
    {
        try {
            jComboBox1.removeAllItems();
        } catch (Exception e) { }
        
        
        if(this.eSigns != null){
            for(LinkedTreeMap eSign: this.eSigns){
                String item = eSign.get("id").toString().replace(".0", "") + " - ";
                item += eSign.get("created_at").toString() + " - ";
                item += eSign.get("table_id").toString() + " - ";
                item += eSign.get("source_record_id").toString().replace(".0", "");

                jComboBox1.addItem(item);
            }
        }
        
        this.eSignComboboxItemChanged();
    }
    
    private void eSignComboboxItemChanged()
    {
        if(this.eSigns == null) return;
        
        int i = jComboBox1.getSelectedIndex();
        this.currentESign = this.eSigns.get(i);
        
        this.fillFormWithESign(this.currentESign);
    }
    
    private void fillFormWithESign(LinkedTreeMap eSign)
    {
        String signedText = eSign.get("signed_text").toString();
        String createdAt = eSign.get("created_at").toString();
        
        String html = "<html><p style=\"height:100%;vertical-align: top;text-align: justify;font-size:14px\">";
        html += signedText + "<br><br>";
        html += "</p><div style='float: right'>"+createdAt+"</div></html>";
        
        this.jLabel2.setText(html);
    }
    
    private boolean eSignsCompare(ArrayList<LinkedTreeMap> list1, ArrayList<LinkedTreeMap> list2)
    {
        if(list1 == null && list2 == null) return true;        
        else if(list1 == null) return false;
        else if(list2 == null) return false;
        
        if(list1.size() != list2.size()) return false;
        
        for(LinkedTreeMap e1: list1){
            boolean control = false;
            
            for(LinkedTreeMap e2: list2){
                String e1Id = e1.get("id").toString();
                String e2Id = e2.get("id").toString();
                
                if(e1Id.equals(e2Id))
                {
                    control = true;
                    break;
                }
            }
            
            if(!control) return false;
        }
        
        return true;
    }
    
    private void clearESigns()
    {
        this.eSigns = null;
        this.currentESign = null;
        jLabel2.setText("<html><p style=\"height:100%;vertical-align: top;text-align: justify;font-size:20px\">Şuan imzalanacak bir belgeniz yok.</p></html>");
        this.setFormElementEnables(false);
    }
    
    private Integer getWaitTimeForESingControl(float waitTime)
    {
        int period;
        
        if(this.eSigns != null) period = 30 * 1000;
        else if(waitTime < 0.05) period = 3 * 1000;
        else if(waitTime < 0.1) period = 9 * 1000;
        else if(waitTime < 0.5) period = 15 * 1000;
        else period = 30 * 1000;
        
        return period;
    }
    
    private LinkedTreeMap getESignControlDataFromServer(boolean force) throws IOException
    {
        Session session = GeneralHelper.getSession();

        String url = session.apiBaseUrl + session.token + "/eSignControl";
        if(force) url += "?force=true";

        LinkedTreeMap data = session.httpGet(url);
        return data;
    }

    /**
     * This method is called from within the constructor to initialize the form.
     * WARNING: Do NOT modify this code. The content of this method is always
     * regenerated by the Form Editor.
     */
    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        jLabel1 = new javax.swing.JLabel();
        jProgressBar1 = new javax.swing.JProgressBar();
        jButton1 = new javax.swing.JButton();
        jComboBox1 = new javax.swing.JComboBox<>();
        jButton2 = new javax.swing.JButton();
        jButton3 = new javax.swing.JButton();
        jPanel1 = new javax.swing.JPanel();
        jLabel2 = new javax.swing.JLabel();
        jButton4 = new javax.swing.JButton();
        jButton5 = new javax.swing.JButton();
        jCheckBox1 = new javax.swing.JCheckBox();
        jCheckBox2 = new javax.swing.JCheckBox();

        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE);
        setPreferredSize(new java.awt.Dimension(748, 512));

        jLabel1.setText("Durum: ");

        jButton1.setIcon(new javax.swing.ImageIcon("D:\\İşler\\Multi\\Angaryos\\GitHub\\e-imza\\img\\reload.png")); // NOI18N
        jButton1.setText("Güncelle");
        jButton1.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                jButton1ActionPerformed(evt);
            }
        });

        jComboBox1.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                jComboBox1ActionPerformed(evt);
            }
        });

        jButton2.setIcon(new javax.swing.ImageIcon("D:\\İşler\\Multi\\Angaryos\\GitHub\\e-imza\\img\\ok.png")); // NOI18N
        jButton2.setText("İmzala");
        jButton2.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                jButton2ActionPerformed(evt);
            }
        });

        jButton3.setIcon(new javax.swing.ImageIcon("D:\\İşler\\Multi\\Angaryos\\GitHub\\e-imza\\img\\cancel.png")); // NOI18N
        jButton3.setText("Reddet");
        jButton3.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                jButton3ActionPerformed(evt);
            }
        });

        jLabel2.setFont(new java.awt.Font("Tahoma", 0, 12)); // NOI18N
        jLabel2.setHorizontalAlignment(javax.swing.SwingConstants.CENTER);
        jLabel2.setText("<html><p style=\"height:100%;vertical-align: top;text-align: justify;font-size:20px\">Şuan imzalanacak bir belgeniz yok.</p></html>");
        jLabel2.setHorizontalTextPosition(javax.swing.SwingConstants.LEADING);
        jLabel2.setVerticalTextPosition(javax.swing.SwingConstants.TOP);

        javax.swing.GroupLayout jPanel1Layout = new javax.swing.GroupLayout(jPanel1);
        jPanel1.setLayout(jPanel1Layout);
        jPanel1Layout.setHorizontalGroup(
            jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addComponent(jLabel2)
        );
        jPanel1Layout.setVerticalGroup(
            jPanel1Layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(jPanel1Layout.createSequentialGroup()
                .addComponent(jLabel2, javax.swing.GroupLayout.DEFAULT_SIZE, 399, Short.MAX_VALUE)
                .addContainerGap())
        );

        jButton4.setIcon(new javax.swing.ImageIcon("D:\\İşler\\Multi\\Angaryos\\GitHub\\e-imza\\img\\ok.png")); // NOI18N
        jButton4.setText("Tümünü İmzala");
        jButton4.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                jButton4ActionPerformed(evt);
            }
        });

        jButton5.setIcon(new javax.swing.ImageIcon("D:\\İşler\\Multi\\Angaryos\\GitHub\\e-imza\\img\\cancel.png")); // NOI18N
        jButton5.setText("Tümünü Reddet");
        jButton5.setToolTipText("");
        jButton5.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                jButton5ActionPerformed(evt);
            }
        });

        jCheckBox1.setText("Otomatik imza at");
        jCheckBox1.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                jCheckBox1ActionPerformed(evt);
            }
        });

        jCheckBox2.setText("Talep gelince öne getir");
        jCheckBox2.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                jCheckBox2ActionPerformed(evt);
            }
        });

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addComponent(jProgressBar1, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addComponent(jPanel1, javax.swing.GroupLayout.Alignment.TRAILING, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                    .addGroup(layout.createSequentialGroup()
                        .addComponent(jComboBox1, 0, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.UNRELATED)
                        .addComponent(jButton1))
                    .addGroup(layout.createSequentialGroup()
                        .addComponent(jButton4)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addComponent(jButton5)
                        .addGap(18, 18, 18)
                        .addComponent(jCheckBox1, javax.swing.GroupLayout.PREFERRED_SIZE, 116, javax.swing.GroupLayout.PREFERRED_SIZE)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addComponent(jCheckBox2)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED, 25, Short.MAX_VALUE)
                        .addComponent(jButton2)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addComponent(jButton3))
                    .addGroup(layout.createSequentialGroup()
                        .addComponent(jLabel1)
                        .addGap(0, 0, Short.MAX_VALUE)))
                .addContainerGap())
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, layout.createSequentialGroup()
                .addGap(13, 13, 13)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jComboBox1, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(jButton1))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(jPanel1, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.UNRELATED)
                .addComponent(jLabel1)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jButton3)
                    .addComponent(jButton2)
                    .addComponent(jButton4)
                    .addComponent(jButton5)
                    .addComponent(jCheckBox1)
                    .addComponent(jCheckBox2))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(jProgressBar1, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
        );

        pack();
    }// </editor-fold>//GEN-END:initComponents

    private void jButton1ActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButton1ActionPerformed
        try {
            GeneralHelper.runAsync(new Callable<Void>() {
                @Override
                public Void call() throws Exception {
                    MainWindow mw = GeneralHelper.getMainWindow();                
                    mw.setFormElementEnables(false);
                    mw.eSignControl(true);
                    mw.setFormElementEnables(true);

                    return null;
                }
            });
        } 
        catch (Exception e) {
            Log.send(e);
        }
    }//GEN-LAST:event_jButton1ActionPerformed

    private void jComboBox1ActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jComboBox1ActionPerformed
        if(this.loadingState) return;
        
        this.eSignComboboxItemChanged();
    }//GEN-LAST:event_jComboBox1ActionPerformed

    private boolean disableEsignOnServer(LinkedTreeMap eSign)
    {
        try {
            String id = eSign.get("id").toString().replace(".0", "");
        
            Session session = GeneralHelper.getSession();
            String url = session.apiBaseUrl + session.token + "/tables/e_signs/"+id+"/update";

            List<NameValuePair> data = new ArrayList<NameValuePair>(2);
            data.add(new BasicNameValuePair("column_set_id", GeneralHelper.formColumnSetId+""));
            data.add(new BasicNameValuePair("in_form_column_name", "state"));
            data.add(new BasicNameValuePair("single_column", "state"));
            data.add(new BasicNameValuePair("state", "0"));

            LinkedTreeMap rt = session.httpPost(url, data);        
            String m = rt.get("message").toString();
            
            return m.equals("success");
            
        } catch (Exception e) {
            return false;
        }        
    }
    
    private void jButton3ActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButton3ActionPerformed
        try {
            int dialogResult = JOptionPane.showConfirmDialog (null, "Emin misiniz?", "Uyarı", JOptionPane.YES_NO_OPTION);
            if(dialogResult != JOptionPane.YES_OPTION) return;
                    
            GeneralHelper.runAsync(new Callable<Void>() {
                @Override
                public Void call() throws Exception {
                    MainWindow mw = GeneralHelper.getMainWindow();                
                    
                    mw.loading(true);
                    mw.setFormElementEnables(false);

                    boolean control = mw.disableEsignOnServer(mw.currentESign);
                    if(!control)
                    {
                        jLabel1.setText("Reddetme başarısız oldu!");
                        return null;
                    }
                    
                    mw.eSigns.remove(mw.currentESign);
                    
                    if(mw.eSigns.size() == 0) 
                    {
                        mw.eSigns = null;
                        mw.currentESign = null;
                        jLabel2.setText("");
                        mw.eSignControlAsync(2 * 1000, true);
                    }
                    
                    mw.eSignsUpdated();
                    if(mw.eSigns != null && mw.eSigns.size() > 0) mw.setFormElementEnables(true);
                    
                    mw.loading(false);

                    return null;
                }
            });
        } 
        catch (Exception e) {
            Log.send(e);
        }
    }//GEN-LAST:event_jButton3ActionPerformed

    private void jButton5ActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButton5ActionPerformed
        try {
            int dialogResult = JOptionPane.showConfirmDialog (null, "Emin misiniz?", "Uyarı", JOptionPane.YES_NO_OPTION);
            if(dialogResult != JOptionPane.YES_OPTION) return;
                    
            GeneralHelper.runAsync(new Callable<Void>() {
                @Override
                public Void call() throws Exception {
                    MainWindow mw = GeneralHelper.getMainWindow();                
                    
                    mw.loading(true);
                    mw.setFormElementEnables(false);
                    
                    for(LinkedTreeMap eSign: mw.eSigns){
            
                        boolean control = mw.disableEsignOnServer(eSign);
                        if(!control)
                        {
                            jLabel1.setText("Reddetme başarısız oldu!");
                            return null;
                        }

                        try {
                            Thread.sleep(1000);
                        } catch (InterruptedException ex) {  }
                    }

                    mw.eSigns = null;
                    mw.currentESign = null;
                    jLabel2.setText("");
                    mw.eSignControlAsync(2 * 1000, true);
                        
                    mw.eSignsUpdated();
        
                    mw.loading(false);

                    return null;
                }
            });
        } 
        catch (Exception e) {
            Log.send(e);
        }
    }//GEN-LAST:event_jButton5ActionPerformed

    private void jCheckBox1ActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jCheckBox1ActionPerformed
        File f = new File(autoSignPath);
        if(f.exists()) f.delete();        
        if(!jCheckBox1.isSelected()) return;
        
        try {
            f.createNewFile();
        } catch (IOException ex) { }
    }//GEN-LAST:event_jCheckBox1ActionPerformed

    private void jCheckBox2ActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jCheckBox2ActionPerformed
        File f = new File(newESignNotifyPath);
        if(f.exists()) f.delete();        
        if(!jCheckBox2.isSelected()) return;
        
        try {
            f.createNewFile();
        } catch (IOException ex) { }
    }//GEN-LAST:event_jCheckBox2ActionPerformed

    private void jButton2ActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButton2ActionPerformed
        
        try {
            if(!jCheckBox1.isSelected()){
                int dialogResult = JOptionPane.showConfirmDialog (null, "Emin misiniz?", "Uyarı", JOptionPane.YES_NO_OPTION);
                if(dialogResult != JOptionPane.YES_OPTION) return;
            }
                    
            GeneralHelper.runAsync(new Callable<Void>() {
                @Override
                public Void call() throws Exception {
                    MainWindow mw = GeneralHelper.getMainWindow();                
                    
                    mw.loading(true);
                    mw.setFormElementEnables(false);

                    boolean control = mw.sign(mw.currentESign);
                    if(!control)
                    {
                        jLabel1.setText("İmzalama başarısız oldu!");
                        mw.setFormElementEnables(true);
                        return null;
                    }
                    
                    mw.eSigns.remove(mw.currentESign);
                    
                    if(mw.eSigns.size() == 0) 
                    {
                        mw.eSigns = null;
                        mw.currentESign = null;
                        jLabel2.setText("");
                        mw.eSignControlAsync(2 * 1000, true);
                    }
                    
                    mw.eSignsUpdated();
                    if(mw.eSigns != null && mw.eSigns.size() > 0) mw.setFormElementEnables(true);
                    
                    mw.loading(false);

                    return null;
                }
            });
        } 
        catch (Exception e) {
            Log.send(e);
        }
    }//GEN-LAST:event_jButton2ActionPerformed

    private void jButton4ActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButton4ActionPerformed
        try {
            if(!jCheckBox1.isSelected()){
                int dialogResult = JOptionPane.showConfirmDialog (null, "Emin misiniz?", "Uyarı", JOptionPane.YES_NO_OPTION);
                if(dialogResult != JOptionPane.YES_OPTION) return;
            }
                    
            GeneralHelper.runAsync(new Callable<Void>() {
                @Override
                public Void call() throws Exception {
                    MainWindow mw = GeneralHelper.getMainWindow();                
                    
                    mw.loading(true);
                    mw.setFormElementEnables(false);
                    
                    for(LinkedTreeMap eSign: mw.eSigns){
            
                        boolean control = mw.sign(eSign);
                        if(!control)
                        {
                            jLabel1.setText("İmzalama başarısız oldu!");
                            return null;
                        }

                        try {
                            Thread.sleep(1000);
                        } catch (InterruptedException ex) {  }
                    }

                    mw.eSigns = null;
                    mw.currentESign = null;
                    jLabel2.setText("");
                    mw.eSignControlAsync(2 * 1000, true);
                        
                    mw.eSignsUpdated();
        
                    mw.loading(false);

                    return null;
                }
            });
        } 
        catch (Exception e) {
            Log.send(e);
        }
    }//GEN-LAST:event_jButton4ActionPerformed

    public String createESignFile(LinkedTreeMap eSign) throws ESYAException, CMSSignatureException, SmartCardException, IOException
    {
        Signing signing = GeneralHelper.getSigning();
        
        String name = signing.getNewFileName();
        String pass = signing.getPasswordFromUser();

        String signedText = eSign.get("signed_text").toString();
        if(!signing.sing(signedText, pass, name)) return "";
        
        return name;
    }
    
    public boolean sign(LinkedTreeMap eSign) throws ESYAException, CMSSignatureException, SmartCardException, IOException
    {
        String path = this.createESignFile(eSign);
        if(path == "") return false;
        
        path = SigningTestConstants.getDirectory() + "/" + path + ".p7s";
        
        Session session = GeneralHelper.getSession();
        return session.uploadSign(eSign, path);
    }
    
    /**
     * @param args the command line arguments
     */
    public static void main(String args[]) {
        /* Set the Nimbus look and feel */
        //<editor-fold defaultstate="collapsed" desc=" Look and feel setting code (optional) ">
        /* If Nimbus (introduced in Java SE 6) is not available, stay with the default look and feel.
         * For details see http://download.oracle.com/javase/tutorial/uiswing/lookandfeel/plaf.html 
         */
        try {
            for (javax.swing.UIManager.LookAndFeelInfo info : javax.swing.UIManager.getInstalledLookAndFeels()) {
                if ("Nimbus".equals(info.getName())) {
                    javax.swing.UIManager.setLookAndFeel(info.getClassName());
                    break;
                }
            }
        } catch (ClassNotFoundException ex) {
            java.util.logging.Logger.getLogger(MainWindow.class.getName()).log(java.util.logging.Level.SEVERE, null, ex);
        } catch (InstantiationException ex) {
            java.util.logging.Logger.getLogger(MainWindow.class.getName()).log(java.util.logging.Level.SEVERE, null, ex);
        } catch (IllegalAccessException ex) {
            java.util.logging.Logger.getLogger(MainWindow.class.getName()).log(java.util.logging.Level.SEVERE, null, ex);
        } catch (javax.swing.UnsupportedLookAndFeelException ex) {
            java.util.logging.Logger.getLogger(MainWindow.class.getName()).log(java.util.logging.Level.SEVERE, null, ex);
        }
        //</editor-fold>

        /* Create and display the form */
        java.awt.EventQueue.invokeLater(new Runnable() {
            public void run() {
                new MainWindow().setVisible(true);
            }
        });
    }

    public void loading(boolean b) {
        this.loadingState = b;
        jProgressBar1.setIndeterminate(b);
    }

    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton jButton1;
    private javax.swing.JButton jButton2;
    private javax.swing.JButton jButton3;
    private javax.swing.JButton jButton4;
    private javax.swing.JButton jButton5;
    private javax.swing.JCheckBox jCheckBox1;
    private javax.swing.JCheckBox jCheckBox2;
    private javax.swing.JComboBox<String> jComboBox1;
    private javax.swing.JLabel jLabel1;
    private javax.swing.JLabel jLabel2;
    private javax.swing.JPanel jPanel1;
    private javax.swing.JProgressBar jProgressBar1;
    // End of variables declaration//GEN-END:variables
}
