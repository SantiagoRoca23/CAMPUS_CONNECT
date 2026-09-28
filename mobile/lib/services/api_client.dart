import 'dart:convert';
import 'package:http/http.dart' as http;

/// Cliente HTTP base para Campus Connect API.
/// Dev B: completa pantallas Flutter consumiendo estos métodos.
class ApiClient {
  ApiClient({
    this.baseUrl = 'http://127.0.0.1:8000/api/v1',
    this.token,
  });

  final String baseUrl;
  String? token;

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      };

  Future<Map<String, dynamic>> login({
    required String email,
    required String password,
    String deviceName = 'flutter',
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {..._headers, 'Content-Type': 'application/json'},
      body: jsonEncode({
        'email': email,
        'password': password,
        'device_name': deviceName,
      }),
    );

    final body = jsonDecode(response.body) as Map<String, dynamic>;
    if (response.statusCode >= 400) {
      throw Exception(body['message'] ?? 'Error de autenticación');
    }

    token = body['token'] as String?;
    return body;
  }

  Future<Map<String, dynamic>> getSolicitudes() async {
    final response = await http.get(
      Uri.parse('$baseUrl/solicitudes'),
      headers: _headers,
    );
    return jsonDecode(response.body) as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> crearSolicitud(Map<String, dynamic> payload) async {
    final response = await http.post(
      Uri.parse('$baseUrl/solicitudes'),
      headers: {..._headers, 'Content-Type': 'application/json'},
      body: jsonEncode(payload),
    );
    return jsonDecode(response.body) as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getSeguimientos(int solicitudId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/solicitudes/$solicitudId/seguimientos'),
      headers: _headers,
    );
    return jsonDecode(response.body) as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getComentarios(int solicitudId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/solicitudes/$solicitudId/comentarios'),
      headers: _headers,
    );
    return jsonDecode(response.body) as Map<String, dynamic>;
  }
}
