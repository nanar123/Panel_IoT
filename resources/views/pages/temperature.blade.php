@extends('layouts.main')

@section('title_menu', 'Data temperatur')


@section('content')
    <div id="dataTemperature"></div>
@endsection


@push('scripts')
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        let chart;
        const baseUrl = '{{ url('/') }}';

        async function requestData() {
            let endpoint = `${baseUrl}/api/v1/temperatures`;
            let params = {
                limit: 20,
            };

            const result = await fetch(`${endpoint}?${new URLSearchParams(params)}`);
            if (result.ok) {
                const data = await result.json();
                let temperatures = data.data;

                temperatures.forEach((temperature) => {
                    let x = new Date(temperature.created_at).getTime();
                    let y = Number(temperature.value);
                    console.log(x, y);

                    chart.series[0].addPoint([x, y], true, chart.series[0].data.length > 20);
                });
                // setTimeout(requestData, 5000);
            }
        }

        window.addEventListener('load', function() {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'dataTemperature',
                    defaultSeriesType: 'spline',
                    events: {
                        load: requestData
                    }
                },
                title: {
                    text: ''
                },
                xAxis: {
                    type: 'datetime',
                    tickPixelInterval: 150,
                    maxZoom: 20 * 1000
                },
                yAxis: {
                    minPadding: 0.2,
                    maxPadding: 0.2,
                    title: {
                        text: 'Value',
                        margin: 100
                    }
                },
                series: [{
                    name: 'Sensor Suhu',
                    data: []
                }]
            });

            const protocol = 'wss'
            const host = 'q17ae2ee.ala.asia-southeast1.emqxsl.com'
            const port = '8084'
            const url = `${protocol}://${host}:${port}/mqtt`

            const username = 'mentoring'
            const password = 'mentoring'
            const clientId =  `mqtt_${Math.random().toString(16).slice(3)}`;

            const options = {
                clientId,
                clean: true,
                connectTimeout: 4000,
                username,
                password,
                reconnectPeriod: 1000,
                ca: `-----BEGIN CERTIFICATE-----
MIIDrzCCApegAwIBAgIQCDvgVpBCRrGhdWrJWZHHSjANBgkqhkiG9w0BAQUFADBh
MQswCQYDVQQGEwJVUzEVMBMGA1UEChMMRGlnaUNlcnQgSW5jMRkwFwYDVQQLExB3
d3cuZGlnaWNlcnQuY29tMSAwHgYDVQQDExdEaWdpQ2VydCBHbG9iYWwgUm9vdCBD
QTAeFw0wNjExMTAwMDAwMDBaFw0zMTExMTAwMDAwMDBaMGExCzAJBgNVBAYTAlVT
MRUwEwYDVQQKEwxEaWdpQ2VydCBJbmMxGTAXBgNVBAsTEHd3dy5kaWdpY2VydC5j
b20xIDAeBgNVBAMTF0RpZ2lDZXJ0IEdsb2JhbCBSb290IENBMIIBIjANBgkqhkiG
9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4jvhEXLeqKTTo1eqUKKPC3eQyaKl7hLOllsB
CSDMAZOnTjC3U/dDxGkAV53ijSLdhwZAAIEJzs4bg7/fzTtxRuLWZscFs3YnFo97
nh6Vfe63SKMI2tavegw5BmV/Sl0fvBf4q77uKNd0f3p4mVmFaG5cIzJLv07A6Fpt
43C/dxC//AH2hdmoRBBYMql1GNXRor5H4idq9Joz+EkIYIvUX7Q6hL+hqkpMfT7P
T19sdl6gSzeRntwi5m3OFBqOasv+zbMUZBfHWymeMr/y7vrTC0LUq7dBMtoM1O/4
gdW7jVg/tRvoSSiicNoxBN33shbyTApOB6jtSj1etX+jkMOvJwIDAQABo2MwYTAO
BgNVHQ8BAf8EBAMCAYYwDwYDVR0TAQH/BAUwAwEB/zAdBgNVHQ4EFgQUA95QNVbR
TLtm8KPiGxvDl7I90VUwHwYDVR0jBBgwFoAUA95QNVbRTLtm8KPiGxvDl7I90VUw
DQYJKoZIhvcNAQEFBQADggEBAMucN6pIExIK+t1EnE9SsPTfrgT1eXkIoyQY/Esr
hMAtudXH/vTBH1jLuG2cenTnmCmrEbXjcKChzUyImZOMkXDiqw8cvpOp/2PV5Adg
06O/nVsJ8dWO41P0jmP6P6fbtGbfYmbW0W5BjfIttep3Sp+dWOIrWcBAI+0tKIJF
PnlUkiaY4IBIqDfv8NZ5YBberOgOzW6sRBc4L0na4UU+Krk2U886UAb3LujEV0ls
YSEY1QSteDwsOoBrp+uvFRTp2InBuThs4pFsiv9kuXclVzDAGySj4dzp30d8tbQk
CAUw7C29C79Fv1C5qfPrmAESrciIxpg0X40KPMbp1ZWVbd4=
-----END CERTIFICATE-----`,
            }
            const client = mqtt.connect(url, options)

            const topic = 'temperatures'

            client.on('connect', () => {
                console.log('Connected')

                client.subscribe([topic], () => {
                    console.log(`Subscribe to topic '${topic}'`)
                })
            })

            client.on('message', (topic, payload) => {
                console.log('Received Message:', topic, payload.toString())

                // jika data yang diterima adalah data temperatures maka request data
                if (topic === 'temperatures') {
                    requestData();
                }

            })
        });
    </script>
@endpush
