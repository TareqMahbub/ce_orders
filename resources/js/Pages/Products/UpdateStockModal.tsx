import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import SecondaryButton from "@/Components/SecondaryButton";
import PrimaryButton from "@/Components/PrimaryButton";
import Modal from "@/Components/Modal";
import {FormEventHandler, useEffect, useRef, useState} from "react";
import {useForm} from "@inertiajs/react";
import DangerButton from "@/Components/DangerButton";
import axios from 'axios';

interface IProps {
    product: any;
    showUpdateStockModal: boolean;
    setShowUpdateStockModal: (show: boolean) => void;
}

interface IInputs {
    merchant_product_no: string;
    stock_location_id: string;
    stock: number;
    current_stock: number;
}

export default function UpdateStockModal({product, showUpdateStockModal, setShowUpdateStockModal}: Readonly<IProps>) {
    const [needsConfirmation, setNeedsConfirmation] = useState(false);
    const stockInput = useRef<HTMLInputElement>(null);

    const {
        put,
        data,
        setData,
        processing,
        errors,
        clearErrors,
        reset
    } = useForm<IInputs>({
        merchant_product_no: '',
        stock_location_id: '',
        stock: 25,
        current_stock: -1
    });

    useEffect(() => {
        clearErrors()
        reset('stock')
        setNeedsConfirmation(false)

        loadCurrentStock()
    }, [product]);

    useEffect(() => {
        if (!showUpdateStockModal) {
            reset('stock')
            clearErrors()
        }
    }, [showUpdateStockModal]);

    const loadCurrentStock = () => {
        if(product.merchant_product_no && product.stock_location_id) {
            setData('merchant_product_no', product.merchant_product_no)
            setData('stock_location_id', product.stock_location_id)

            const queryParams = {
                merchant_product_no: product.merchant_product_no
            }
            axios.get(route('stock.get', queryParams))
                .then(response => {
                    if('stock' in response.data) {
                        setData('current_stock', response.data['stock'])
                    }
                })
                .catch(error => {
                    console.error('There was an error making the request:', error);
                });
        }
    }

    const addStock: FormEventHandler = (e) => {
        e.preventDefault();

        setNeedsConfirmation(true)
        clearErrors()
    };

    const confirm: FormEventHandler = (e) => {
        e.preventDefault();

        put(route('stock.add'), {
            onSuccess: () => {
                setShowUpdateStockModal(false)
            },
            onError: () => {
                setNeedsConfirmation(false)
            }
        });
    };

    return <Modal
        show={showUpdateStockModal}
        onClose={() => setShowUpdateStockModal(false)}
        maxWidth={'md'}
    >
        <div>
            <form onSubmit={confirm} className="p-6">
                <h2 className="text-lg font-medium text-gray-900">
                    {
                        needsConfirmation
                            ? 'Confirmation'
                            : `Set stock`
                    }
                </h2>

                <div className="mb-4 mt-4 text-sm text-gray-600">
                    Product name: <br/>
                    <span className={'italic'}>{product.product_name}</span>
                </div>

                {
                    data.current_stock > -1
                        ? <div className="mb-4 text-sm text-green-600">
                            Current Stock: # {data.current_stock}
                        </div>
                        : null
                }

                <p className="mt-1 text-sm text-gray-600">
                    {
                        needsConfirmation
                            ? 'Click confirm to add the stock.'
                            : 'Please enter the number of new stocks to add:'
                    }
                </p>

                <div className="mt-3">
                    <InputLabel
                        htmlFor="stock"
                        value="Stock"
                        className="sr-only"
                    />

                    <TextInput
                        id="stock"
                        type="number"
                        name="stock"
                        ref={stockInput}
                        value={data.stock}
                        onChange={(e) =>
                            setData('stock', parseInt(e.currentTarget.value))
                        }
                        className="mt-1 block w-full"
                        isFocused
                        placeholder="stock"
                        disabled={needsConfirmation}
                        min={0}
                    />

                    <InputError
                        message={errors.stock}
                        className="mt-2"
                    />
                </div>

                {
                    needsConfirmation && <div className="mt-6 flex justify-end">
                        <SecondaryButton onClick={() => setNeedsConfirmation(false)}>
                            Edit
                        </SecondaryButton>

                        <DangerButton className="ms-3" disabled={processing}>
                            Please Confirm
                        </DangerButton>
                    </div>
                }
            </form>

            {
                !needsConfirmation && <div className="mt-1 flex justify-end pr-6 pb-6">
                    <SecondaryButton onClick={() => setShowUpdateStockModal(false)}>
                    Cancel
                    </SecondaryButton>

                    <PrimaryButton className="ms-3" disabled={!data.stock} onClick={addStock}>
                        Update Stock
                    </PrimaryButton>
                </div>
            }
        </div>
    </Modal>
}
