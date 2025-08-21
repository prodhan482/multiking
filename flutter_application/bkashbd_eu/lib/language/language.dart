import 'package:bkashbd_eu/language/en.dart';
import 'package:get/get.dart';

class Language extends Translations {
  @override
  // TODO: implement keys
  Map<String, Map<String, String>> get keys => {
        //ENGLISH LANGUAGE
        'en_US': en_fields(),
        'en_GB': en_fields(),
        'en_CA': en_fields(),
        'en_AU': en_fields()
      };
}
