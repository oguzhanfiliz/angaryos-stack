# Angaryos

Merhaba. Bu; yazılımcıları yazılımın angarya kısmından kurtarıp esas işe odaklanmalarını sağlacyaak olan tamamen açık kaynak bir yazılım geliştirme altyapısıdır. 

## Gereksinimler
    -Light Kurulum (startLight.sh): 1 core cpu, 1 GB ram, 15 GB hdd 
    -Full Kurulum (start.sh): 2 core cpu, 3 GB ram, 25 GB hdd

## Kurulum

Bu uygulama, içerisinde bir çok servisi barındırır. Bu sebeple önerilen, sanal bir işletim sistemi[1] üzerinde kurulum yapılmasıdır. 

Light bir kurulum istiyorsanız ./backend/.env içindeki "LOG_CHANNEL" ayarını "LOG_CHANNEL=daily" olarak güncelleyin. Uygulamayı başlatmak için "start.sh" yerine "startLight.sh" dosyasını kullanın.

[1] Testler UbuntuServer16.04 (X64) işletim sistemi üzerinde yapıldığı için önerilmektedir.

### Linux
```
sudo wget https://raw.githubusercontent.com/karapazar/Angaryos/master/start.sh
sudo chmod +x start.sh
sudo ./start.sh
```

### Windows
```
hazırlanıyor...
```

### Mac
```
hazırlanıyor...
```

ardından  "Angaryos/frontend/src/environments" dizini içindeki dosyaları düzenleyiniz. Bu dosyalar IP vb ayarlarınızı içerir.

## Kullanım 

Döküman hazırlanıyor...


**** Varsayılan web arayüz teması bir lisans anahtarına ihtiyaç duyar.