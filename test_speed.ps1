#!/usr/bin/env powershell
$sw = [System.Diagnostics.Stopwatch]::StartNew()
$resp = [System.Net.WebRequest]::Create('http://localhost:8000/login')
$resp.GetResponse()
$sw.Stop()
Write-Host 'TIME_TOTAL: ' $sw.ElapsedMilliseconds 'ms'