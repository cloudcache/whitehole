<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Security-Group Guide</title>
    <link rel="stylesheet" href="style01.css" type="text/css">
</head>
<body>

<table width=100%>
	<tr>
		<td bgcolor=d8ffff width=100 align=center><b>일반
		<td>
			<li>모든 포트는 기본 DROP.
			<li>허용 하려는 프로토콜, IP, 포트(또는 포트 범위)로 대상 지정.
			<li>요청 대상에 대한 허용/거부/차단 정책 지정.
	<tr>
		<td bgcolor=d8ffff width=100 align=center><b>포로토콜
		<td>
			<li>TCP, UDP, ICMP만 지원.
			<li>(주의) ICMP의 경우, 포트 범위는 불필요.
	<tr>
		<td bgcolor=d8ffff width=100 align=center><b>출발지 IP
		<td>
			<li>기본은 CIDR 형식으로 등록한다.
			<li>(예제-1) 192.168.0.12라는 IP하나에 대해서만 일 경우,
				<br>&nbsp; &nbsp; &nbsp; &nbsp;192.168.0.12/32 또는 192.168.0.12
				<br>&nbsp; &nbsp; &nbsp; &nbsp;(단일 IP일 경우에는 "/Bit" 생략 가능. "/32" 자동 지정.)
			<li>(예제-1) 172.21.80.x 대역 모든 IP에 대해서 일 경우,
				<br>&nbsp; &nbsp; &nbsp; &nbsp;172.21.80.0/24
			<li>그외 패턴은 CIDR 문서를 참조.
	<tr>
		<td bgcolor=d8ffff width=100 align=center><b>포트
		<td>
			<li>tcp와 udp에서만 해당. icmp는 해당사항 없음.
			<li>범위(range) 형태로 등록.
			<li>'시작'과 '종료'가 같으면 단일 포트로 인식.
			<li>지정 가능한 범위는 0 ~ 65534 이다.
	<tr>
		<td bgcolor=d8ffff width=100 align=center><b>정책
		<td>
			<li>ACCEPT : 허용.
			<li>REJECT : 거부 (Resused 메세지 리턴 및 차단.)
			<li>DROP : 차단 (리턴 없이 차단.)
</table>

</body>
</html>
