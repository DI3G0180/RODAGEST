#!/usr/bin/env powershell
# Primero obtenemos el CSRF token de la página GET
$getSw = [System.Diagnostics.Stopwatch]::StartNew()
$req = [System.Net.WebRequest]::Create('http://127.0.0.1:8000/login')
$resp = $req.GetResponse()
$getSw.Stop()

# Leer el HTML para obtener el token CSRF
$stream = $resp.GetResponseStream()
$reader = New-Object System.IO.StreamReader($stream)
$html = $reader.ReadToEnd()
$reader.Close()
$stream.Close()

# Extraer el token CSRF (Blade suele tener un campo hidden con nombre _token)
$tokenMatch = [regex]::Match($html, '_token"\s+value="([^"]+)"')
$csrfToken = if ($tokenMatch.Success) { $tokenMatch.Groups[1].Value } else { 'NO TOKEN FOUND' }

Write-Host 'GET /login: OK, tiempo: ' $getSw.ElapsedMilliseconds 'ms'
Write-Host 'CSRF Token encontrado: ' $csrfToken

# Ahora hacer el POST con el token
if ($csrfToken -ne 'NO TOKEN FOUND') {
    $postSw = [System.Diagnostics.Stopwatch]::StartNew()
    
    # Crear el body del formulario
    $body = "_token=$csrfToken&email=admin%40rodagest.com&password=password123&remember="
    
    $postReq = [System.Net.WebRequest]::Create('http://127.0.0.1:8000/login')
    $postReq.Method = 'POST'
    $postReq.ContentType = 'application/x-www-form-urlencoded'
    $postReq.ContentLength = $body.Length
    
    # Escribir el body
    $stream = $postReq.GetRequestStream()
    $writer = New-Object System.IO.StreamWriter($stream)
    $writer.Write($body)
    $writer.Close()
    
    $postResp = $postReq.GetResponse()
    $postSw.Stop()
    
    Write-Host 'POST /login: ' $postSw.ElapsedMilliseconds 'ms'
    Write-Host 'POST Status: ' $postResp.StatusDescription
    $postResp.Close()
} else {
    Write-Host 'No se pudo obtener el token CSRF para el POST'
}