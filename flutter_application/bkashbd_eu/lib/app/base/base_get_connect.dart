import 'package:bkashbd_eu/app/constants.dart';
import 'package:get/get.dart';

class BaseGetConnect extends GetConnect {
  @override
  void onInit() {
    // All request will pass to jsonEncode so CasesModel.fromJson()
    //httpClient.defaultDecoder = CasesModel.fromJson;
    httpClient.baseUrl = Constants.SERVER_API_URL;

    httpClient.defaultContentType = "application/json";
    httpClient.timeout = Duration(seconds: 60);

    /*httpClient.addResponseModifier((request, response) async {
      print(response.headers);
      print(response.bodyString);
      return request;
    });*/

    // It's will attach 'apikey' property on header from all requests
    /*httpClient.addRequestModifier((request) {
      request.headers['apikey'] = '12345678';
      return request;
    });

    // Even if the server sends data from the country "Brazil",
    // it will never be displayed to users, because you remove
    // that data from the response, even before the response is delivered
    httpClient.addResponseModifier<CasesModel>((request, response) {
      CasesModel model = response.body;
      if (model.countries.contains('Brazil')) {
        model.countries.remove('Brazilll');
      }
    });

    httpClient.addAuthenticator((request) async {
      final response = await get("http://yourapi/token");
      final token = response.body['token'];
      // Set the header
      request.headers['Authorization'] = "$token";
      return request;
    });*/

    //Autenticator will be called 3 times if HttpStatus is
    //HttpStatus.unauthorized
    httpClient.maxAuthRetries = 3;

    super.onInit();
  }

  //@override
  //Future<Response<CasesModel>> getCases(String path) => get(path);
}
