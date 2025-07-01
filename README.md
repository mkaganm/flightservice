# Flight Service Projesi

Bu proje, PHP ile geliştirilmiş bir uçuş arama ve fiyatlama servisidir. Hem REST hem de SOAP üzerinden uçuş arama, fiyat sorgulama ve rezervasyon işlemleri için temel bir altyapı sunar. Proje, AirArabia gibi havayolları sağlayıcılarına entegre olabilecek şekilde esnek ve genişletilebilir olarak tasarlanmıştır.

REST tabanlı uçuş arama servisi başarıyla çalışmaktadır. Tek yön, gidiş-dönüş ve çoklu uçuş (multi-city) aramaları desteklenmektedir. Tüm uçuş fiyatlarına otomatik olarak %10 hizmet bedeli eklenmektedir.

Flight şemasında, flightPrice alanı servis ücretinin eklenmiş olduğu son kullanıcıya gösterilen uçuş fiyatını ifade eder. providerPrice ise doğrudan sağlayıcıdan (provider) alınan ham uçuş fiyatıdır. Bu ayrım sayesinde, uçuş fiyatları üzerinde esnek bir fiyatlandırma ve şeffaflık sağlanır.

Kullanıcı doğrulama (auth) işlemlerinde, oturum (token) yönetimi hızlı ve geçici erişim için in-memory (bellek içi) olarak tutulmaktadır. Kullanıcı giriş yaptığında üretilen token'lar, `src/Services/InMemory/InMemoryStorage.php` dosyasında saklanır ve her istek geldiğinde buradan kontrol edilir. 

SOAP entegrasyonu ise geliştirme aşamasındadır ve henüz tam olarak tamamlanmamıştır. İlerleyen sürümlerde SOAP ile de uçuş arama ve fiyatlama desteği sağlanacaktır.


## Proje Dosya Yapısı (Detaylı Açıklama)

```
flightservice/
├── composer.json           # Proje bağımlılıklarını ve autoload ayarlarını tanımlar
├── composer.lock           # Yüklenen bağımlılıkların kesin sürümlerini kilitler
├── docker-compose.yml      # Docker ile çoklu servis çalıştırmak için (isteğe bağlı)
├── Dockerfile              # Projenin Docker imajı için yapılandırma (isteğe bağlı)
├── Makefile                # Sık kullanılan komutlar (install, test, lint, start, clean)
├── README.md               # Proje dokümantasyonu
├── public/                 # Uygulamanın dışarıya açılan ana dizini
│   └── index.php           # HTTP isteklerinin giriş noktası (front controller)
├── src/                    # Tüm uygulama kaynak kodları
│   ├── Controllers/        # HTTP endpoint/controller sınıfları (örn: SearchController)
│   ├── Models/             # Temel veri modelleri (örn: Flight, CabinPrices, PaxPrices)
│   ├── Providers/          # Havayolu sağlayıcı entegrasyonları
│   │   └── AirArabia/      # AirArabia'ya özel tüm entegrasyon kodları
│   │       ├── AirArabiaProvider.php   # AirArabia ana provider sınıfı
│   │       ├── REST/                   # AirArabia REST entegrasyonları
│   │       │   ├── Builder/            # REST isteklerini oluşturan yardımcılar
│   │       │   ├── Parser/             # REST response ayrıştırıcıları
│   │       │   └── Services/           # REST servis katmanı
│   │       └── SOAP/                   # AirArabia SOAP entegrasyonları
│   │           ├── Builder/            # SOAP istek builder sınıfları
│   │           ├── Parser/             # SOAP response parser sınıfları
│   │           └── Services/           # SOAP servis katmanı (örn: AirPriceReq)
│   └── Services/           # Uygulama iş servisleri (örn: FlightPriceService, FlightSearchService)
│       └── InMemory/       # Geçici bellek tabanlı servisler (örn: InMemoryStorage)
├── test_auth.php           # Test amaçlı örnek dosya (kullanıcı doğrulama)
├── test_search.php         # Test amaçlı örnek dosya (arama işlemleri)
└── vendor/                 # Composer ile yüklenen tüm harici PHP paketleri
    └── ...                # (guzzle, symfony, psr, dotenv vb. kütüphaneler)
```

### Klasör Açıklamaları

- **public/**: Uygulamanın dışarıya açılan tek dizinidir. Tüm HTTP istekleri index.php üzerinden yönlendirilir (Front Controller Pattern).
- **src/Controllers/**: API veya web endpoint'lerini yöneten controller sınıfları burada bulunur.
- **src/Models/**: Uçuş, yolcu, kabin fiyatı gibi temel veri modelleri burada tanımlanır.
- **src/Providers/**: Farklı havayolu veya servis sağlayıcılarına ait tüm entegrasyon kodları burada yer alır. Her sağlayıcı için ayrı bir klasör açılır.
- **src/Providers/AirArabia/REST ve SOAP/**: AirArabia'nın REST ve SOAP API'lerine özel builder, parser ve servis katmanları.
- **src/Services/**: Uygulamanın iş mantığı (business logic) burada bulunur. Fiyat hesaplama, arama gibi servisler.
- **src/Services/InMemory/**: Geçici, bellekte tutulan servisler (ör: test veya demo amaçlı).

### Servis, Builder ve Parser Katmanları Açıklaması

- **Auth Servisi:**
  - Kullanıcı doğrulama ve yetkilendirme işlemlerini yönetir.
  - Genellikle `src/Controllers/` altında bir AuthController ile başlar, iş mantığı ise ayrı bir AuthService sınıfında bulunur.
  - Giriş/çıkış, token üretimi, kullanıcı kontrolü gibi işlemler burada yapılır.

- **Search Servisi (REST Örneği):**
  - Uçuş arama işlemlerini REST API üzerinden yönetir.
  - `src/Controllers/SearchController.php` dosyası HTTP isteklerini karşılar ve gelen arama parametrelerini alır.
  - Arama ile ilgili iş mantığı `src/Services/FlightSearchService.php` içinde bulunur; burada hangi sağlayıcıdan arama yapılacağına karar verilir.
  - REST tabanlı sağlayıcılar için arama sonuçlarının işlenmesi `src/Providers/AirArabia/REST/Parser/FlightSearchResponseParser.php` gibi parser sınıfları ile yapılır.
  - Arama isteklerinin oluşturulması için `src/Providers/AirArabia/REST/Builder/FlightSearchRequestBuilder.php` gibi builder sınıfları kullanılır.

- **Builder Sınıfları (REST Örneği):**
  - Dış REST servislere gönderilecek arama veya fiyat isteklerinin (request) doğru formatta ve standartta oluşturulmasını sağlar.
  - Her sağlayıcı için ayrı builder sınıfları bulunur (örn. `FlightSearchRequestBuilder`, `AirPriceRequestBuilder`).
  - Örnek: `src/Providers/AirArabia/REST/Builder/FlightSearchRequestBuilder.php` uçuş arama için, `src/Providers/AirArabia/REST/Builder/AirPriceRequestBuilder.php` fiyat sorgulama için kullanılır.

- **Parser Sınıfları (REST Örneği):**
  - Dış REST servislerden gelen yanıtların (response) uygulamanın anlayacağı veri yapılarına dönüştürülmesini sağlar.
  - Her sağlayıcı için ayrı parser sınıfları bulunur (örn. `FlightSearchResponseParser`, `AirPriceResponseParser`).
  - Örnek: `src/Providers/AirArabia/REST/Parser/FlightSearchResponseParser.php` uçuş arama yanıtı için, `src/Providers/AirArabia/REST/Parser/AirPriceResponseParser.php` fiyat yanıtı için kullanılır.

Bu katmanlar sayesinde hem servislerin bakımı kolaylaşır hem de yeni sağlayıcı/entegrasyon eklemek çok daha hızlı ve güvenli olur.

### REST Katmanı Örnekleri

- **REST Builder Örneği:**
  - `src/Providers/AirArabia/REST/Builder/FlightSearchRequestBuilder.php` dosyası, AirArabia REST API'sine gönderilecek uçuş arama isteklerini doğru formatta hazırlar.
  - Parametreleri alır, gerekli alanları doldurur ve dış servise uygun bir dizi/JSON oluşturur.

- **REST Parser Örneği:**
  - `src/Providers/AirArabia/REST/Parser/FlightSearchResponseParser.php` dosyası, AirArabia REST API'sinden dönen uçuş arama yanıtlarını uygulamanın anlayacağı veri yapılarına dönüştürür.
  - Gelen JSON veya dizi içinden uçuş, fiyat, segment gibi bilgileri ayrıştırır ve sadeleştirir.

- **REST Service Örneği:**
  - `src/Providers/AirArabia/REST/Services/FlightSearchService.php` dosyası, uçuş arama işleminin iş mantığını yönetir.
  - Builder ile istek oluşturur, REST API'ye gönderir, Parser ile yanıtı işler ve sonucu döner.

Bu yapı sayesinde REST tabanlı tüm işlemler modüler, test edilebilir ve kolayca genişletilebilir olur. Her sağlayıcı için benzer builder, parser ve service dosyaları oluşturulabilir.

### FlightPriceService Açıklaması

- **FlightPriceService:**
  - `src/Services/FlightPriceService.php` dosyasında bulunur.
  - Uçuş fiyatlarına servis ücreti eklemek gibi fiyatlama ile ilgili iş mantığını yönetir.
  - Örneğin, `updatePrice` metodu ile her uçuşun fiyatına belirli bir oranla (örn. %10) hizmet bedeli ekler ve güncellenmiş fiyatı döner.
  - Fiyat hesaplama işlemleri merkezi olarak burada toplandığı için, farklı fiyatlama stratejileri veya ek ücretler kolayca yönetilebilir.
  - Diğer servisler veya controller'lar, uçuş fiyatı ile ilgili tüm işlemler için bu servisi kullanır.

### Auth Token'ın InMemory'de Tutulması

Projede kullanıcı doğrulama (auth) işlemleri sırasında üretilen token'lar, hızlı ve geçici erişim için `src/Services/InMemory/InMemoryStorage.php` dosyasında bellekte (in-memory) tutulur. Bu yapı sayesinde:

- Kullanıcı giriş yaptığında bir token üretilir ve InMemoryStorage'a kaydedilir.
- Her istek geldiğinde, gönderilen token InMemoryStorage üzerinden kontrol edilir.
- Çıkış yapıldığında veya token süresi dolduğunda, ilgili token InMemoryStorage'dan silinir.
- Bu yöntem, test ve geliştirme ortamları için hızlı ve kolay bir çözüm sunar. Gerçek ortamda ise Redis gibi kalıcı bir in-memory store tercih edilebilir.

**Avantajları:**
- Disk veya veritabanı erişimi olmadan çok hızlı token kontrolü sağlar.
- Kodun test edilebilirliğini ve sadeliğini artırır.

**Dikkat:**
- InMemoryStorage sunucu yeniden başlatıldığında tüm token'lar silinir.
- Üretim ortamında kalıcı ve dağıtık bir çözüm (Redis, Memcached) tercih edilmelidir.

### Models (Veri Modelleri) Açıklaması

- **src/Models/** klasörü, uygulamanın temel veri yapılarını ve iş nesnelerini içerir. Her model, uçuş arama ve fiyatlama süreçlerinde kullanılan veri tiplerini temsil eder.

  - **Flight.php:** Bir uçuşun temel bilgilerini (kalkış-varış, segmentler, fiyatlar, vs.) tutar.
  - **CabinPrices.php:** Bir uçuşun kabin sınıfı bazında fiyat bilgisini ve yolcu tipine göre fiyatları içerir.
  - **PaxPrices.php:** Yolcu tipine (yetişkin, çocuk, bebek) göre fiyat bilgisini tutar.
  - **SearchRequest.php:** Uçuş arama için gerekli olan parametreleri (kalkış, varış, tarih, yolcu sayısı, vs.) kapsar.
  - **Segment.php:** Bir uçuşun tekil segmentini (ör: bir bacak) ve segmentle ilgili detayları (kalkış, varış, saat, havayolu, vs.) içerir.

Bu modellerin temel amacı, farklı sağlayıcıların (provider) döndürdüğü karmaşık ve değişken veri yapılarını standart, sade ve kolay yönetilebilir bir forma dönüştürmektir. Böylece servis ve controller katmanları, tüm sağlayıcılar için aynı model arayüzünü kullanarak iş mantığını basit ve sürdürülebilir şekilde kurabilir. Yani modeller, provider bağımsız bir uygulama mimarisi sağlar.

## Örnek REST Uçuş Arama Request'i

Aşağıda, REST API üzerinden uçuş arama işlemi için kullanılabilecek örnek bir JSON request görebilirsiniz:

```
{
  "from": "CAI",
  "to": "MCT",
  "departureDate": "2025-08-24",
  "returnDate": "2025-08-28",
  "adultCount": 1,
  "childCount": 1,
  "infantCount": 1
}
```

## Örnek REST Uçuş Arama Response'u

Aşağıda, uçuş arama işlemi sonucunda dönebilecek örnek bir JSON response görebilirsiniz:

```
[
  {
    "segments": [
      {
        "flightNumber": "E51099",
        "origin": "CAI",
        "destination": "MCT",
        "originTerminal": "T1",
        "destinationTerminal": "",
        "originCountryCode": "EG",
        "destinationCountryCode": "OM",
        "segmentCode": "CAI/MCT",
        "departureTime": "2025-08-24T17:00:00",
        "arrivalTime": "2025-08-24T22:40:00"
      }
    ],
    "cabinPrices": [
      {
        "cabinClass": "Y",
        "fareFamily": "Y",
        "price": 967.438,
        "classCodeKey": "CAI/MCT",
        "classCodeValue": "Y4",
        "paxPrices": [
          { "paxCode": "ADT", "paxPrice": 346.237 },
          { "paxCode": "CHD", "paxPrice": 346.237 },
          { "paxCode": "INF", "paxPrice": 274.965 }
        ]
      }
    ],
    "flightPrice": 1064.1818,
    "providerPrice": 967.438
  },
  {
    "segments": [
      {
        "flightNumber": "E51321",
        "origin": "MCT",
        "destination": "CAI",
        "originTerminal": "",
        "destinationTerminal": "T1",
        "originCountryCode": "OM",
        "destinationCountryCode": "EG",
        "segmentCode": "MCT/CAI",
        "departureTime": "2025-08-28T05:00:00",
        "arrivalTime": "2025-08-28T08:00:00"
      }
    ],
    "cabinPrices": [
      {
        "cabinClass": "Y",
        "fareFamily": "Y",
        "price": 1167.636,
        "classCodeKey": "MCT/CAI",
        "classCodeValue": "Y1",
        "paxPrices": [
          { "paxCode": "ADT", "paxPrice": 465.971 },
          { "paxCode": "CHD", "paxPrice": 465.971 },
          { "paxCode": "INF", "paxPrice": 235.696 }
        ]
      }
    ],
    "flightPrice": 1284.3996,
    "providerPrice": 1167.636
  }
]
```
