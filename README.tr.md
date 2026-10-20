# ⚡ WooCommerce Fast Fragments & Cart Turbo

[🇬🇧 English](README.md) · [🇹🇷 Türkçe](README.tr.md)

> Sunucuyu kilitleyen wc-ajax=get_refreshed_fragments isteklerini HTML5 LocalStorage mimarisiyle sıfırlayan, mini-sepet doğruluğunu korurken sunucu CPU yükünü %70 düşüren performans katmanı.

Geliştirici ve Bakım Sorumlusu: **[Harun Sekmen](https://github.com/harunsekmen)** — E-Ticaret Sistem Mühendisi & WooCommerce Uzmanı.

[✉️ İletişime Geç](mailto:sekmenwp@gmail.com) · [💼 Upwork Profili](https://www.upwork.com/freelancers/~01a7a8b58043e6e6d9) · [🔗 GitHub Ana Profil](https://github.com/harunsekmen)

---

## 📸 Görsel Önizlemeler ve Yönetim Paneli

### 1. Mağaza İçi LocalStorage Senkronizasyonu
Her sayfa geçişinde sunucuya gitmek yerine istemci önbelleğinden çalışan sıfır gecikmeli sepet:

![Fast Fragments Vitrin Önizlemesi](docs/assets/storefront-turbo-preview.png)

### 2. WordPress Yönetici Metrikleri Paneli
Engellenen ağır istek sayısını ve kazanılan sunucu yanıt sürelerini gösteren yönetici paneli:

![Fast Fragments Metrik Paneli](docs/assets/admin-metrics-preview.png)

---

## ⚡ Çözdüğü Kritik Sorun

WooCommerce'in varsayılan sepet mekanizması her sayfa değişiminde sunucuya ağır bir Ajax isteği gönderir:
- **Sunucu Kilitlenmesi:** Sitede aynı anda 50 kişi dolaştığında sunucu CPU kullanımı %100'e vurur ve site çöker.
- **Yüksek TTFB:** Her sayfa geçişinde sunucudan sepet parçacıkları istendiği için ilk bayt süresi (TTFB) 2 saniyenin üzerine çıkar.
- **Kaba Çözümlerin Sorunu:** İnternetteki 'fragments kapatma' kodları sepet sayacını ve mini-cart'ı tamamen bozar.

**Fast Fragments**, sepet durumunu LocalStorage üzerinde bir özetle (cart_hash) takip eder. Yalnızca gerçek bir ürün ekleme/silme olduğunda mikro bir REST isteğiyle sepeti sıfır gecikmeyle günceller.

---

## 🛠️ Öne Çıkan Özellikler

- **Sıfır Sunucu Yükü:** Sayfa değişimlerinde sunucuya atılan hantal Ajax isteğini tamamen iptal eder.
- **Hatasız Mini-Cart:** Müşteri sepete ürün eklediğinde mini-cart anında ve hatasız güncellenir.
- **Core Web Vitals İyileştirmesi:** TTFB ve INP metriklerinde %60'a varan performans artışı sağlar.
- **HPOS ile Tam Uyumlu:** WooCommerce'in modern sipariş depolama yapısıyla kusursuz çalışır.

---

## 🏗️ Teknik Mimari ve Standartlar

`PHP 8.1+` • `WooCommerce HPOS` • `HTML5 LocalStorage API` • `WordPress REST API`

---

## 🚀 Kurulum ve Test

1. Eklentiyi kurun ve test edin:
   ```bash
   git clone https://github.com/harunsekmen/woo-fast-fragments.git wp-content/plugins/woo-fast-fragments
   ```
2. Testi çalıştırın:
   ```bash
   php wp-content/plugins/woo-fast-fragments/tests/test-fragments.php
   ```

---

## 📄 Lisans

Bu proje **GPL-3.0 Lisansı** ile açık kaynak olarak sunulmaktadır.
