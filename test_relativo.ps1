#!/usr/bin/env powershell
$sw = [System.Diagnostics.Stopwatch]::StartNew()
$req = [System.Net.WebRequest]::Create('http://127.0.0.1:8000/login')
$resp = $req.GetResponse()
$sw.Stop()
Write-Host 'GET /login: ' $sw.ElapsedMilliseconds 'ms'
Write-Host 'Status: ' $resp.StatusDescription
$resp.Close()