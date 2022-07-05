let base_url_twilio = $("#hdd_const_voice").val();
let hdd_id_operador = $("#hdd_id_operador").val();
  const speakerDevices = document.getElementById("speaker-devices");
  const ringtoneDevices = document.getElementById("ringtone-devices");
  const outputVolumeBar = document.getElementById("output-volume");
  const inputVolumeBar = document.getElementById("input-volume");
  const volumeIndicators = document.getElementById("volume-indicators");
  const callButton = document.getElementById("btn_call");
  const outgoingCallHangupButton = document.getElementById("btn_hang");
  const callControlsDiv = document.getElementById("call-controls");
  const audioSelectionDiv = document.getElementById("output-selection");
  const getAudioDevicesButton = document.getElementById("get-devices");
  const logDiv = document.getElementById("log");
  const incomingCallDiv = document.getElementById("incoming-call");

  const incomingCallHangupButton = document.getElementById(
    "button-hangup-incoming"
  );
  const incomingCallAcceptButton = document.getElementById(
    "button-accept-incoming"
  );
  const incomingCallRejectButton = document.getElementById(
    "button-reject-incoming"
  );
  const phoneNumberInput = document.getElementById("txt_num_man");
  const incomingPhoneNumberEl = document.getElementById("incoming-number");
  const startupButton = document.getElementById("startup-button");
  let device;
  let token;

  
  // Event Listeners
  if (callButton == null){
    callButton ==""
  }else{
    callButton
    callButton.onclick = (e) => {
      e.preventDefault();
      makeOutgoingCall();
    };
  }
   getAudioDevices();
  // speakerDevices.addEventListener("change", updateOutputDevice);
  // ringtoneDevices.addEventListener("change", updateRingtoneDevice);
  // SETUP STEP 1:
  // Browser client should be started after a user gesture
  // to avoid errors in the browser console re: AudioContext
  startupButton.addEventListener("click", function(){startupClient()});
  // SETUP STEP 2: Request an Access get_voice_response

async function startupClient(callback = () => {}) {
  console.log("Requesting Access Token...");
  $("#body_status_call").text("   Peticion de token de acceso...");
  try {
    const data = await $.getJSON(base_url_twilio+"api/Videollamada/get_access_token/"+hdd_id_operador);
    $("#body_status_call").text("   Token optenido...");
    console.log("Got a token.");
    token = data.token;
    // setClientNameUI(data.identity);
    intitializeDevice();
    callback();
  } catch (err) {
    console.log(err);
    console.log("An error occurred. See your browser console for more information.");
  }
}
// SETUP STEP 3:
// Instantiate a new Twilio.Device
function intitializeDevice() {
//   logDiv.classList.remove("hide");
  console.log("Initializing device");
  $("#body_status_call").text("   Inicializando dispositivo...");
  device = new Twilio.Device(token, {
    logLevel: 1,
    // Set Opus as our preferred codec. Opus generally performs better, requiring less bandwidth and
    // providing better audio quality in restrained network conditions.
    codecPreferences: ["opus", "pcmu"]
  });
  addDeviceListeners(device);
  // Device must be registered in order to receive incoming calls
  device.register();
}
// SETUP STEP 4:
// Listen for Twilio.Device states
function addDeviceListeners(device) {
  device.on("registered", function () {
    console.log("Twilio.Device Ready to make and receive calls!");
    $("#body_status_call").text("   Softphone listo para recibir y enviar llamadas...");
    // callControlsDiv.classList.remove("hide");
  });
  device.on("error", function (error) {
    console.log("Twilio.Device Error: " + error.message);
  });
  device.on("incoming", handleIncomingCall);
  device.audio.on("deviceChange", updateAllAudioDevices.bind(device));
  // Show audio selection UI if it is supported by the browser.
  if (device.audio.isOutputSelectionSupported) {
    // audioSelectionDiv.classList.remove("hide");
  }
}
// MAKE AN OUTGOING CALL
async function makeOutgoingCall() {
  // debugger
  var params = {
    // get the phone number to call from the DOM
    To: phoneNumberInput.value,
  };
  if (device) {
    console.log(`Attempting to call ${params.To} ...`);
    if ( btn_call_action_id != "")
    {

      $("#"+btn_call_action_id).css({'background-color': '#ba0000', 'color': 'white'});
      // $("#"+btn_call_action_id).css({'background-color': '#ba0000', 'color': 'white'});
      toastr["success"](`Realizando llamada al numero: ${params.To}`, "Llamando");
      sessionStorage.llamada_saliente = 1;
    }

    $("#body_status_call").text(`   Intentando llamar a: ${params.To} ...`);
    // Twilio.Device.connect() returns a Call object)

    const call = await device.connect({ params });
    
     console.log(call)
    
    // add listeners to the Call
    // "accepted" means the call has finished connecting and the state is now "open"
    call.on("accept", updateUIAcceptedOutgoingCall);
    call.on("disconnect", updateUIDisconnectedOutgoingCall);
    call.on("cancel", updateUIDisconnectedOutgoingCall);
    outgoingCallHangupButton.onclick = () => {
            console.log("Hanging up ...");
            $("#body_status_call").text("   Colgando...");
            call.disconnect();
            if ( btn_call_action_id != "")
              {
                sessionStorage.llamada_saliente = 0;
              }
            
    };
  } else {
    console.log("Unable to make call.");
  }
}
async function updateUIAcceptedOutgoingCall(call) {
 
  // await fetch("https://testvoice.solventa.co/insert_call", {
  //     method: "POST",
  //     headers: {
  //       "Content-Type": "application/json",
  //       "Access-Control-Allow-Origin": "*"
  //     },
  //     crossDomain: true,
  //     credentials: 'same-origin',
  //     body: JSON.stringify({
  //       CallSid: call.parameters.CallSid,
  //       status: "calling",
  //     }),
  //   }).then((response) => {
  //     if (response.ok) {
  //       console.log("Call status updated");
  //     } else {
  //       console.log("Error updating call status");
  //     }
  //   });

    // $.ajax({
    //   type: "POST",
    //   crossDomain: true,
    //   data:{CallSid: call.parameters.CallSid,status: "calling"},
    //   dataType: 'jsonp',
    //   url: 'https://testvoice.solventa.co/insert_call',
    //   beforesend: function () {
    //     request.setRequestHeader("Access-Control-Allow-Origin", '*');
    //   },
    //   success: function (response) {

    //     if (response.ok) {
    //       console.log("Call status updated");
    //     } else {
    //       console.log("Error updating call status");
    //     }


    //   }
    // });

  $("#body_status_call").text("   Llamada en progreso...");
  
  console.log("Call in progress ...");
  callButton.disabled = true;
  outgoingCallHangupButton.classList.remove("hide");
  volumeIndicators.classList.remove("hide");
  bindVolumeIndicators(call);
}
function updateUIDisconnectedOutgoingCall() {
  console.log("Call disconnected.");
  $("#body_status_call").text("   Llamada desconectada...");
  callButton.disabled = false;
  sessionStorage.llamada_saliente = 0;
//   outgoingCallHangupButton.classList.add("hide");
//   volumeIndicators.classList.add("hide");
}
// HANDLE INCOMING CALL
function handleIncomingCall(call) {
  console.log(`Incoming call from ${call.parameters.From}`);
  $("#body_status_call").text(`   Llamada entrante de ${call.parameters.From} ...`);
  //show incoming call div and incoming phone number
  incomingCallDiv.classList.remove("hide");
  incomingPhoneNumberEl.innerHTML = call.parameters.From;
  //add event listeners for Accept, Reject, and Hangup buttons
  incomingCallAcceptButton.onclick = () => {
    acceptIncomingCall(call);
  };
  incomingCallRejectButton.onclick = () => {
    rejectIncomingCall(call);
  };
  incomingCallHangupButton.onclick = () => {
    hangupIncomingCall(call);
  };
  // add event listener to call object
  call.on("cancel", handleDisconnectedIncomingCall);
  call.on("disconnect", handleDisconnectedIncomingCall);
  call.on("reject", handleDisconnectedIncomingCall);
}
// ACCEPT INCOMING CALL
function acceptIncomingCall(call) {
  call.accept();
  //update UI
  console.log("Accepted incoming call.");
  $("#body_status_call").text("   Llamada entrante aceptada...");
  incomingCallAcceptButton.classList.add("hide");
  incomingCallRejectButton.classList.add("hide");
  incomingCallHangupButton.classList.remove("hide");
}
// REJECT INCOMING CALL
function rejectIncomingCall(call) {
  call.reject();
  console.log("Rejected incoming call");
  $("#body_status_call").text("   Llamada entrante rechazada...");
  resetIncomingCallUI();
}
// HANG UP INCOMING CALL
function hangupIncomingCall(call) {
  call.disconnect();
  console.log("Hanging up incoming call");
  $("#body_status_call").text("   Llamada entrante colgada...");
  resetIncomingCallUI();
}
// HANDLE CANCELLED INCOMING CALL
function handleDisconnectedIncomingCall() {
  $("#body_status_call").text("   Llamada entrante terminada...");
  console.log("Incoming call ended.");
  resetIncomingCallUI();
}
// MISC USER INTERFACE
// Activity log
function log(message) {
    console.log(message)
    //   logDiv.innerHTML += `<p class="log-entry">&gt;&nbsp; ${message} </p>`;
    //   logDiv.scrollTop = logDiv.scrollHeight;
}
function setClientNameUI(clientName) {
  var div = document.getElementById("client-name");
  div.innerHTML = `Your client name: <strong>${clientName}</strong>`;
}
function resetIncomingCallUI() {
  incomingPhoneNumberEl.innerHTML = "";
  incomingCallAcceptButton.classList.remove("hide");
  incomingCallRejectButton.classList.remove("hide");
  incomingCallHangupButton.classList.add("hide");
  incomingCallDiv.classList.add("hide");
}
// AUDIO CONTROLS
async function getAudioDevices() {
  await navigator.mediaDevices.getUserMedia({ audio: true });
  updateAllAudioDevices.bind(device);
}
function updateAllAudioDevices() {
  if (device) {
    // updateDevices(speakerDevices, device.audio.speakerDevices.get());
    // updateDevices(ringtoneDevices, device.audio.ringtoneDevices.get());
    device.audio.speakerDevices.get()
    device.audio.ringtoneDevices.get()
  }
}
// function updateOutputDevice() {
//   const selectedDevices = Array.from(speakerDevices.children)
//     .filter((node) => node.selected)
//     .map((node) => node.getAttribute("data-id"));
//   device.audio.speakerDevices.set(selectedDevices);
// }
// function updateRingtoneDevice() {
//   const selectedDevices = Array.from(ringtoneDevices.children)
//     .filter((node) => node.selected)
//     .map((node) => node.getAttribute("data-id"));
//   device.audio.ringtoneDevices.set(selectedDevices);
// }
function bindVolumeIndicators(call) {
  call.on("volume", function (inputVolume, outputVolume) {
    var inputColor = "red";
    if (inputVolume < 0.5) {
      inputColor = "green";
    } else if (inputVolume < 0.75) {
      inputColor = "yellow";
    }
    inputVolumeBar.style.width = Math.floor(inputVolume * 300) + "px";
    inputVolumeBar.style.background = inputColor;
    var outputColor = "red";
    if (outputVolume < 0.5) {
      outputColor = "green";
    } else if (outputVolume < 0.75) {
      outputColor = "yellow";
    }
    outputVolumeBar.style.width = Math.floor(outputVolume * 300) + "px";
    outputVolumeBar.style.background = outputColor;
  });
}
// Update the available ringtone and speaker devices
// function updateDevices(selectEl, selectedDevices) {
//   selectEl.innerHTML = "";
//   device.audio.availableOutputDevices.forEach(function (device, id) {
//     var isActive = selectedDevices.size === 0 && id === "default";
//     selectedDevices.forEach(function (device) {
//       if (device.deviceId === id) {
//         isActive = true;
//       }
//     });
//     var option = document.createElement("option");
//     option.label = device.label;
//     option.setAttribute("data-id", id);
//     if (isActive) {
//       option.setAttribute("selected", "selected");
//     }
//     selectEl.appendChild(option);
//   });
// }