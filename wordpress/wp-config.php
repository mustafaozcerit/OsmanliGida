<?php

// BEGIN iThemes Security - Do not modify or remove this line
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Disable File Editor - Security > Settings > WordPress Tweaks > File Editor
// END iThemes Security - Do not modify or remove this line

/**
 * WordPress için taban ayar dosyası.
 *
 * Bu dosya şu ayarları içerir: MySQL ayarları, tablo öneki,
 * gizli anahtaralr ve ABSPATH. Daha fazla bilgi için
 * {@link https://codex.wordpress.org/Editing_wp-config.php wp-config.php düzenleme}
 * yardım sayfasına göz atabilirsiniz. MySQL ayarlarınızı servis sağlayıcınızdan edinebilirsiniz.
 *
 * Bu dosya kurulum sırasında wp-config.php dosyasının oluşturulabilmesi için
 * kullanılır. İsterseniz bu dosyayı kopyalayıp, ismini "wp-config.php" olarak değiştirip,
 * değerleri girerek de kullanabilirsiniz.
 *
 * @package WordPress
 */

// ** MySQL ayarları - Bu bilgileri sunucunuzdan alabilirsiniz ** //
/** WordPress için kullanılacak veritabanının adı */
define( 'DB_NAME', 'anatoliadb' );

/** MySQL veritabanı kullanıcısı */
define( 'DB_USER', 'root' );

/** MySQL veritabanı parolası */
define( 'DB_PASSWORD', '' );

/** MySQL sunucusu */
define( 'DB_HOST', 'localhost' );

/** Yaratılacak tablolar için veritabanı karakter seti. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Veritabanı karşılaştırma tipi. Herhangi bir şüpheniz varsa bu değeri değiştirmeyin. */
define('DB_COLLATE', '');

/**#@+
 * Eşsiz doğrulama anahtarları.
 *
 * Her anahtar farklı bir karakter kümesi olmalı!
 * {@link http://api.wordpress.org/secret-key/1.1/salt WordPress.org secret-key service} servisini kullanarak yaratabilirsiniz.
 * Çerezleri geçersiz kılmak için istediğiniz zaman bu değerleri değiştirebilirsiniz. Bu tüm kullanıcıların tekrar giriş yapmasını gerektirecektir.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'dATz.S/]Q,F=!UfH);RZr}n| 33Oh^4L99+p#JRD@U{G^]jB_)!qT1qN1ca/m-DH' );
define( 'SECURE_AUTH_KEY',  '__!b!HX_c<6NZ+~dY8sa(a0$1xW L-(/c^2aXGt=K9ziDAwKlloIh:+Jy!v*jKm5' );
define( 'LOGGED_IN_KEY',    'wz[_uO/JT@nj^61.2CkedQc~^t`<y3:j,LSkr3^`F`P5wfF/Q@mdr6a~cg+b%z}/' );
define( 'NONCE_KEY',        'YOw[<>dI$:kw_siH(}{O7)uzeOVUP#)#*{4hqu=5y~:1|<G;x<v*Qzn(,Pc:SGHq' );
define( 'AUTH_SALT',        'v`<to$Lr[IGv#:J+|N]bb)O` 6.we;%/fd,qpTsjEG:fn1uN[&mC:=TL576b`?|4' );
define( 'SECURE_AUTH_SALT', '-`l~jHLIY8u9p;G1vmJ!39e uVC42k9PM=Vz_/bWZ*IK75sGTG[$}%)yc1uL%XVY' );
define( 'LOGGED_IN_SALT',   'qkxril+9;1XP3B4goVtAD[R:7yAMPMB/-o8;t?T%Zz}w D|/6g374G;JzxmYN(A-' );
define( 'NONCE_SALT',       '_?&d@|jU b-yrgpW1_@l~o#wA5xsQk*nJ_V+K7ZZGdb@V&0}F+n5[%wr^@kE8 #t' );
/**#@-*/

/**
 * WordPress veritabanı tablo ön eki.
 *
 * Tüm kurulumlara ayrı bir önek vererek bir veritabanına birden fazla kurulum yapabilirsiniz.
 * Sadece rakamlar, harfler ve alt çizgi lütfen.
 */
$table_prefix = 'wp_';

/**
 * Geliştiriciler için: WordPress hata ayıklama modu.
 *
 * Bu değeri "true" yaparak geliştirme sırasında hataların ekrana basılmasını sağlayabilirsiniz.
 * Tema ve eklenti geliştiricilerinin geliştirme aşamasında WP_DEBUG
 * kullanmalarını önemle tavsiye ederiz.
 */
define('WP_DEBUG', false);

/* Hepsi bu kadar. Mutlu bloglamalar! */

/** WordPress dizini için mutlak yol. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** WordPress değişkenlerini ve yollarını kurar. */
require_once(ABSPATH . 'wp-settings.php');
