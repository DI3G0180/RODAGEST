#!/usr/bin/env powershell
# Test 1: GET /login
$sw = [System.Diagnostics.Stopwatch]::StartNew()
$req = [System.Net.WebRequest]::Create('http://localhost:8000/login')
$resp = $req.GetResponse()
$sw.Stop()
$http = $resp.StatusCode
$ct = $resp.ContentType
$resp.Close()
Write-Host "LOGIN HTTP: $http | Tiempo: $($sw.Elapsed.TotalSeconds)s | ContentType: $ct`n"

# Test 2: GET /images/logo-rodagest.png
$sw2 = [System.Diagnostics.Stopwatch]::StartNew()
$req2 = [System.Net.WebRequest]::Create('http://localhost:8000/images/logo-rodagest.png')
$resp2 = $req2.GetResponse()
$sw2.Stop()
$http2 = $resp2.StatusCode
$ct2 = $resp2.ContentType
$resp2.Close()
Write-Host "LOGO HTTP: $http2 | Tipo: $ct2`n"

# Test 3: GET /dashboard
$sw3 = [System.Diagnostics.Stopwatch]::StartNew()
$req3 = [System.Net.WebRequest]::Create('http://localhost:8000/dashboard')
$resp3 = $req3.GetResponse()
$sw3.Stop()
$http3 = $resp3.StatusCode
$resp3.Close()
Write-Host "DASHBOARD HTTP: $http3 | Tiempo: $($sw3.Elapsed.TotalSeconds)s`n"