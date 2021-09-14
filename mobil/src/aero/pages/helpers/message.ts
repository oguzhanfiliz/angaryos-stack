import { Injectable } from '@angular/core';

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

declare var $: any;

export type Types = 'success' | 'error' | 'warning' | 'info' | 'question';
export type inputTypes = 'text' | 'textarea' ;

@Injectable()
export class MessageHelper 
{   
    constructor( ) 
    {
    } 
    
    public toastMessage(message: string, type:string = "info", duration:number = 3000)
    {
        let temp: string = type;
        let iconType: Types = temp as Types; 

        const Toast = Swal.mixin(
        {
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: duration
        })

        Toast.fire(
        {
            icon: iconType,
            title: message
        });
    }
    
    public swalPrompt(title, confirmText = "Tamam", cancelText = "İptal", inputType = "text")
    {
        let temp: string = inputType;
        let tempInputType: inputTypes = temp as inputTypes; 
        
        return Swal.fire(
        {
            title: title,
            input: tempInputType,
            inputAttributes: 
            {
              autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: confirmText,
        });
    }
    
    public swalComboBox(title, inputOptions, confirmText = "Tamam", cancelText = "İptal")
    {
        return Swal.fire(
        {
            title: title,
            input: "select",
            inputOptions: inputOptions,
            showCancelButton: true,
            confirmButtonText: confirmText,
        });
    }
    
    public swalConfirm(title, text, icon, confirmButtonText = 'Evet', cancelButtonText = 'Hayır', showCancelButton = true)
    {
        return Swal.fire(
        {
            title: title,
            text: text,
            icon: icon,
            showCancelButton: showCancelButton,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButtonText,
            cancelButtonText: cancelButtonText
        })
        .then((r) => 
        {
            return r.value;
        });
    }

    public sweetAlert(message, title = "Mesaj", icon = "info")
    {
        let temp: string = icon;
        let iconType: Types = temp as Types;

        Swal.fire({
            title: title,
            html: message,
            icon: iconType
        });
    }
}